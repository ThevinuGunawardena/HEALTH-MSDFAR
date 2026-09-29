<?php

namespace backend\config;

use Yii;
use yii\db\Query;

class UserTypeUtil
{
    /**
     * profile_officer.current_workplace_type value that means head office.
     * MUST match the dropdown value in the profile _form.php exactly.
     */
    const WORKPLACE_HEAD_OFFICE = 'Head Office';

    public static function hasType($value)
    {
        $types = explode(',', Yii::$app->user->identity->type);
        return in_array((string)$value, $types);
    }

    public static function isDirector()
    {
        return self::hasType(Constant::DIRECTOR);
    }

    public static function isDG()
    {
        return self::hasType(Constant::DG); // 4
    }

    // ================================================================
    //  Leave approval routing (by WORKPLACE, then by DISTRICT)
    // ================================================================

    /**
     * Can the CURRENTLY logged-in user approve THIS leave request?
     *
     * Routing:
     *   - own request                    -> never
     *   - DG (4) applicant               -> nobody (top of chain)
     *   - AD (3) / ITD (15) applicant    -> the DG
     *   - Director (999) applicant       -> the DG   (legacy accounts)
     *   - Head-Office posting            -> the IT Director (15)
     *   - District-Office posting        -> the AD (3) of the SAME district
     *   - District with no AD account    -> the DG   (fallback)
     *
     * Routing for ordinary staff turns on the applicant's WORKPLACE, not
     * their user_type: whoever sets their profile to Head Office is approved
     * by the ITD, whoever sets it to District Office is approved by their
     * district's AD.
     *
     * @param \backend\models\Leave $leave
     * @return bool
     */
    public static function canCurrentUserApprove($leave)
    {
        // Nobody approves their own request.
        if ((int) $leave->user_id === (int) Yii::$app->user->id) {
            return false;
        }

        $applicantType = (int) $leave->user_type;

        // Top of chain: the DG's own leave has no approver here.
        if ($applicantType === Constant::DG) {
            return false;
        }

        // AD / ITD (and legacy Director accounts) are approved by the DG.
        if (self::isDgApprovedType($applicantType)) {
            return self::isDG();
        }

        // ── Everyone else routes on WHERE THEY WORK, not on their role ──

        // Head Office -> the IT Director, whatever district sits on the
        // profile. A head-office posting is a head-office posting.
        if (self::leaveIsHeadOffice($leave)) {
            return self::hasType(Constant::ITD);
        }

        // District Office -> the Assistant Director of that district,
        // Colombo included. Division does not change who approves; it only
        // narrows who can be named as acting officer.
        $applicantDistrict = self::leaveDistrict($leave);

        // Unroutable (no district on the profile) or the district has no AD
        // account: fall back to the DG rather than stranding the request
        // with nobody able to action it.
        if ($applicantDistrict === null || !self::districtHasAd($applicantDistrict)) {
            return self::isDG();
        }

        if (!self::hasType(Constant::AD)) {
            return false;
        }

        $myDistrict = self::currentUserDistrict();

        return $myDistrict !== null
            && (int) $myDistrict === (int) $applicantDistrict;
    }

    /**
     * The approver user-TYPE for a leave (used only for the notification
     * label — the real permission check is canCurrentUserApprove()).
     *
     * @param \backend\models\Leave $leave
     * @return int|null
     */
    public static function approverTypeForLeave($leave)
    {
        $applicantType = (int) $leave->user_type;

        if ($applicantType === Constant::DG) {
            return null;
        }
        if (self::isDgApprovedType($applicantType)) {
            return Constant::DG;
        }

        if (self::leaveIsHeadOffice($leave)) {
            return Constant::ITD;
        }

        // District office -> that district's AD, or the DG where no AD
        // account exists for the district.
        $district = self::leaveDistrict($leave);

        return ($district !== null && self::districtHasAd($district))
            ? Constant::AD
            : Constant::DG;
    }

    // ---------------------------------------------------------------
    //  Posting resolution
    // ---------------------------------------------------------------

    /**
     * Is this leave request from a HEAD OFFICE officer?
     *
     * Reads the snapshot taken on the leave row at submit time, and falls
     * back to the live profile for rows created before that column existed.
     *
     * @param \backend\models\Leave $leave
     * @return bool
     */
    public static function leaveIsHeadOffice($leave)
    {
        $snapshot = trim((string) ($leave->applicant_workplace_type ?? ''));
        if ($snapshot !== '') {
            return $snapshot === self::WORKPLACE_HEAD_OFFICE;
        }

        return self::isHeadOfficeApplicant($leave->user_id);
    }

    /**
     * District this leave request belongs to, from the snapshot where
     * available, otherwise from the applicant's current profile.
     *
     * @param \backend\models\Leave $leave
     * @return int|null
     */
    public static function leaveDistrict($leave)
    {
        if (!empty($leave->applicant_district)) {
            return (int) $leave->applicant_district;
        }

        $district = self::applicantDistrict($leave->user_id);

        return ($district === null || $district === '') ? null : (int) $district;
    }

    /**
     * Does this district have at least one Assistant Director account?
     *
     * Called once per approval check and once per dashboard load, so the
     * answer is memoised for the request.
     *
     * @param int $district
     * @return bool
     */
    public static function districtHasAd($district)
    {
        return in_array((int) $district, self::districtsWithAd(), true);
    }

    /**
     * District ids that have at least one AD account.
     *
     * user.type may hold a comma-separated list (e.g. "3,11"), so this
     * matches on membership rather than equality — an AD who also holds
     * another role must still be found.
     *
     * @return int[]
     */
    public static function districtsWithAd()
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $rows = (new Query())
            ->select('po.district')
            ->distinct()
            ->from('user u')
            ->innerJoin('profile_officer po', 'po.id = u.profile_id')
            ->where(new \yii\db\Expression(
                "FIND_IN_SET(:adType, REPLACE(u.type, ' ', '')) > 0",
                [':adType' => (string) Constant::AD]
            ))
            ->andWhere(['not', ['po.district' => null]])
            ->column();

        $cache = array_map('intval', $rows);

        return $cache;
    }

    /**
     * Subquery of user ids that are DISTRICT-office staff of one district,
     * optionally narrowed to a single division.
     *
     * @param int      $district
     * @param int|null $division
     * @return Query
     */
    public static function districtOfficeUserIdQuery($district, $division = null)
    {
        $q = self::districtStaffUserIdQuery($district);

        if (!empty($division)) {
            $q->andWhere(['po.division' => $division]);
        }

        return $q;
    }

    /**
     * True if this applicant type SKIPS the CC (supervising officer)
     * review stage: Additional Director (3) and IT Director (15).
     * Their leave goes straight from the acting officer to the DG.
     *
     * @param int|string $applicantType
     * @return bool
     */
    public static function isCcExemptType($applicantType)
    {
        return in_array((int) $applicantType, Constant::CC_EXEMPT_TYPES, true);
    }

    /**
     * True if this applicant type's leave is approved by the DG:
     * Director (999), Additional Director (3), IT Director (15).
     *
     * @param int|string $applicantType
     * @return bool
     */
    public static function isDgApprovedType($applicantType)
    {
        return in_array((int) $applicantType, Constant::DG_APPROVAL_TYPES, true);
    }

    /**
     * Legacy type-only routing (kept for backward compatibility).
     * Prefer approverTypeForLeave() / canCurrentUserApprove().
     */
    public static function approverTypeFor($applicantType)
    {
        $applicantType = (int) $applicantType;

        if ($applicantType === Constant::DG) {
            return null;
        }
        if ($applicantType === Constant::DIRECTOR) {
            return Constant::DG;
        }
        return Constant::DIRECTOR;
    }

    // ---------------------------------------------------------------
    //  Workplace / district lookups
    // ---------------------------------------------------------------

    /** True if the given user's officer profile is marked Head Office. */
    public static function isHeadOfficeApplicant($userId)
    {
        return self::headOfficeUserIdQuery()
            ->andWhere(['u.id' => $userId])
            ->exists();
    }

    /** District id on the CURRENT user's officer profile (or null). */
    public static function currentUserDistrict()
    {
        $profileId = Yii::$app->user->identity->profile_id ?? null;
        if (!$profileId) {
            return null;
        }
        $district = (new Query())
            ->select('district')
            ->from('profile_officer')
            ->where(['id' => $profileId])
            ->scalar();

        return ($district === false || $district === null || $district === '') ? null : $district;
    }

    /** District id on the given applicant's officer profile (or null). */
    public static function applicantDistrict($userId)
    {
        $district = (new Query())
            ->select('po.district')
            ->from('user u')
            ->innerJoin('profile_officer po', 'po.id = u.profile_id')
            ->where(['u.id' => $userId])
            ->scalar();

        return ($district === false || $district === null || $district === '') ? null : $district;
    }

    /**
     * Subquery of user ids whose officer profile is Head Office.
     * Used by LeaveController::scopeForApprover() for the ITD queue.
     * @return Query
     */
    public static function headOfficeUserIdQuery()
    {
        return (new Query())
            ->select('u.id')
            ->from('user u')
            ->innerJoin('profile_officer po', 'po.id = u.profile_id')
            ->where(['po.current_workplace_type' => self::WORKPLACE_HEAD_OFFICE]);
    }

    /**
     * Subquery of user ids that are DISTRICT-office staff of one district.
     * "District office" = anything that is NOT explicitly Head Office
     * (so a blank workplace_type still counts as district).
     * @param int $district
     * @return Query
     */
    public static function districtStaffUserIdQuery($district)
    {
        return (new Query())
            ->select('u.id')
            ->from('user u')
            ->innerJoin('profile_officer po', 'po.id = u.profile_id')
            ->where(['po.district' => $district])
            ->andWhere(['or',
                ['<>', 'po.current_workplace_type', self::WORKPLACE_HEAD_OFFICE],
                ['po.current_workplace_type' => null],
            ]);
    }

    // ---------------------------------------------------------------

    public static function hasPrimaryType($value)
    {
        $types = explode(',', Yii::$app->user->identity->type);

        // Get first type, trim it (very important!)
        $primary = trim($types[0] ?? '');

        return $primary === (string)$value;
    }

    public static function getTypeNames($type)
    {
        $types = explode(',', $type);
        $typeNames = [];

        foreach ($types as $type) {
            $type = trim($type); // Clean up any extra whitespace
            if (isset(Constant::$userTypes[$type])) {
                $typeNames[] = Constant::$userTypes[$type]['name'];
            } else {
                $typeNames[] = $type; // Fallback to raw value if no mapping exists
            }
        }

        return implode(' | ', $typeNames);
    }
}