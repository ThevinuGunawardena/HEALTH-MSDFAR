<?php

namespace backend\services;

use backend\config\Constant;
use backend\config\UserTypeUtil;
use backend\models\ApprovalLog;
use backend\models\BoatNumbers;
use backend\models\DistrictGearTypes;
use backend\models\ExportCompany;
use backend\models\FishermanImpersonateLog;
use backend\models\FishermanRegisterdBoatLicense;
use backend\models\HighseasLicense;
use backend\models\MApprovalWorkflow;
use backend\models\MBoatCategory;
use backend\models\MBoatTypes;
use backend\models\MDivision;
use backend\models\MFiDistrict;
use backend\models\MFishTypes;
use backend\models\MGearTypes;
use backend\models\MHarbours;
use backend\models\MLandingSite;
use backend\models\MMainGearTypes;
use backend\models\MZone;
use backend\models\NationalLicense;
use backend\models\ProfileOfficer;
use backend\models\ProfileYard;
use backend\models\User;
use Da\QrCode\QrCode;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UnauthorizedHttpException;

class CommonService
{

    /**
     * @throws UnauthorizedHttpException
     */
    public static function validatePermission($controller, $action)
    {
        if (Yii::$app->user->getIsGuest()) return $controller->goHome();
        if (!Yii::$app->user->can($action)) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));
        }


    }

    public static function validateLoginUser($controller)
    {
        if (Yii::$app->user->getIsGuest()) return $controller->goHome();


    }

    public static function validateEditPermission()
    {
        if (!Util::adminPermission() && !Util::editPermission()) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));
        }
    }

    public static function validateEditPermissionBoolean()
    {
        if (!Util::adminPermission() && !Util::editPermission()) {
            return false;
        }
        return true;
    }

    public static function validateCurrentUserAuthForMethod($profileId)
    {
        if ($profileId == Yii::$app->user->identity->profile_id) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission to run this operation.'));
        }
    }

    /**
     * @throws UnauthorizedHttpException
     */
    public static function logValidateErrors($model)
    {
        if (isset($_GET["logErrors"])) {
            $model->validate();
            if ($_GET["logErrors"] == "model") {
                print_r($model);
            }
            print_r("----------------------------------------");
            print_r($model->getErrors());
            exit();
        }


    }

    public static function addApprovalLog($type, $status, $remark, $processId)
    {
        $model = new ApprovalLog();
        $model->type = $type;
        $model->process_id = $processId;
        $model->done_by = Yii::$app->user->identity->id;
        $model->status = $status;
        $model->remark = $remark;
        $model->date_time = date("Y-m-d H:i:s");
        $model->save();
    }

    public static function addApprovalLogdone($type, $status, $remark, $processId, $doneby)
    {
        $ex = ApprovalLog::find()->where(["type" => $type, "status" => $status, "remark" => $remark, "process_id" => $processId, "done_by" => $doneby])->one();
        if ($ex == null) {
            $model = new ApprovalLog();
            $model->type = $type;
            $model->process_id = $processId;
            $model->done_by = $doneby;
            $model->status = $status;
            $model->remark = $remark;
            $model->date_time = date("Y-m-d H:i:s");
            $model->save();
            print_r($processId);
            print_r("saved");
            print_r("\n");
        }
    }

    public static function addImpersonateLog($fishermanId, $mainIdentityId, $status)
    {
        $impersonateLog = new FishermanImpersonateLog();
        $impersonateLog->status = $status;
        $impersonateLog->fisherman_id = $fishermanId;
        $impersonateLog->impersonated_by = $mainIdentityId;
        $impersonateLog->date_time = date("Y-m-d H:i");
        $impersonateLog->save();
    }

    public static function getApprovalProcess($model, $processType, $prosessId, $smallBoat)
    {
        if ($processType == "SKIPPER_LICENCE" && $model->renew == 1) {
            $approvalHistory = ApprovalLog::find()->where(['type' => "SKIPPER_LICENCE_RENEW", "process_id" =>
                $model->renew_id])->all();

        } else {
            $approvalHistory = ApprovalLog::find()->where(['type' => $processType, "process_id" => $prosessId])->all();

        }
        $approvalWorkflow = MApprovalWorkflow::findOne(['type' => $processType]);
        $showRejectBtn = false;
        $showApproveBtn = false;
        $nextStage = "";
        $currentStage = $model->approval_stage;
        $approvalStages = explode(",", $approvalWorkflow->workflow);
        if (UserTypeUtil::hasType($currentStage)) {
            $showRejectBtn = true;
            $showApproveBtn = true;
            $currentKey = array_search($currentStage, $approvalStages);
            if ($currentStage == Constant::AD && $smallBoat == true) {
                $nextStage = "Approved";
            } else {
                $nextStage = $approvalStages[$currentKey + 1] ?? "Approved";

            }

        }
        return [
            "approvalHistory" => $approvalHistory,
            "showRejectBtn" => $showRejectBtn,
            "showApproveBtn" => $showApproveBtn,
            "nextStage" => $nextStage,
            "approvalStages" => $approvalStages,
            "paymentRequired" => $approvalWorkflow->payment_requred,
            "expiredIn" => $approvalWorkflow->expired_in,

        ];


    }

    /**
     * @param array $approvalFlow
     * @param int $id
     * @return void
     */
    public static function markApprovalStage(array $approvalFlow, $model, int $id, $processType)
    {
        $data = Yii::$app->request->post();
        if ($data['status-approval'] == "approve") {
            $model->approval_stage = $approvalFlow['nextStage'];
            if ($approvalFlow['nextStage'] == "Approved") {
                if ($approvalFlow['paymentRequired'] == 0) {
                    $model->status = Constant::Active;
                    $model->approval_stage = "Completed";
                    $model->approved_time = date("Y-m-d H:i");
                    if ($model->expire_date == null)
                        $model->expire_date = self::getExpireDate($approvalFlow['expiredIn'], self::getRenewLicenseExpireDate($model, $processType));
                } else {
                    $model->status = 99;

                }
            }
        } else {
            $model->approval_stage = $approvalFlow['approvalStages'][0];
        }
        CommonService::addApprovalLog($processType, $data['status-approval'], $data['remarks-approval'] != "" ? $data['remarks-approval'] : "-", $id);

        return $model;
    }

    /**
     * @param array $approvalFlow
     * @param int $id
     * @return void
     */
    public static function markApprovalStageForBoatCancellationAndTransfer($approvalFlow, $model, $id, $processType)
    {
        $data = Yii::$app->request->post();
        if ($data['status-approval'] == "approve") {
            $model->approval_stage = $approvalFlow['nextStage'];
            if ($approvalFlow['nextStage'] == "Approved") {
                if ($approvalFlow['paymentRequired'] == 0) {
                    $model->status = Constant::RequestCompleted;
                    $model->approval_stage = "Completed";
                    $model->approved_time = date("Y-m-d H:i");

                } else {
                    $model->status = 99;

                }
            }
        } else {
            $model->approval_stage = $approvalFlow['approvalStages'][0];
        }
        CommonService::addApprovalLog($processType, $data['status-approval'], $data['remarks-approval'], $id);

        return $model;
    }

    public static function getBoatCategoryArray(): array
    {
        return ArrayHelper::map(MBoatCategory::find()->where(["status" => 1])->orderBy("code")->asArray()->all(), 'id', "code");
    }

    public static function getFIDistrictArray(): array
    {
        return ArrayHelper::map(MFiDistrict::find()->where(["status" => 1])->orderBy("name")->asArray()->all(), 'id', "name");
    }

    public static function getYardsArray(): array
    {
        return ArrayHelper::map(ProfileYard::find()->where(["status" => Constant::Active])->orderBy("name")->asArray()->all(), 'id', "name");
    }

    public static function getBoatCategoriesArray(): array
    {
        return ArrayHelper::map(MBoatTypes::find()->where(["status" => 1])->orderBy("code")->asArray()->all(), 'id', "code");
    }

    public static function getBoatTypesArray(): array
    {
        return ArrayHelper::map(MBoatTypes::find()->where(["status" => 1])->asArray()->all(), 'id', "code");
    }

    public static function getHarboursArray(): array
    {
        return ArrayHelper::map(MHarbours::find()->where(["status" => 1])->orderBy("Name")->asArray()->all(), 'Id', "Name");
    }

    public static function getLandingSitesArray($division = 0): array
    {

        if ($division != 0) {
            $divisions = MDivision::find()->select("id")->where(["district_id" => $division, "status" => 1])->asArray()->all();
            foreach ($divisions as $division) {
                $divisionsS[] = $division['id'];
            }
//            $divisionsS[]=176;
//            ArrayHelper::
//            $divisionsS[]=255;
//            print_r($divisionsS);exit();
            return ArrayHelper::map(MLandingSite::find()->where(["status" => 1])->andWhere(['IN', "division_id", $divisionsS])->asArray()->all(), 'id', "name");

        }
        return ArrayHelper::map(MLandingSite::find()->where(["status" => 1])->orderBy("name")->asArray()->all(), 'id', "name");
    }


    public static function getGearTypesArray(): array
    {
        return ArrayHelper::map(MGearTypes::find()->asArray()->all(), 'id', "description");
    }

    public static function getMainGearTypesArray(): array
    {
        return ArrayHelper::map(MMainGearTypes::find()->asArray()->all(), 'id', "description");
    }

    public static function getDivisionGearTypesArray($gear, $division): array
    {
        return DistrictGearTypes::find()->where(["division" => $division])->andWhere(["status" => 1])->all();
    }

    public static function getYearsArray(): array
    {
        return array_combine(range(date("Y"), 1900), range(date("Y"), 1900));
    }

    public static function getFishTypesArray(): array
    {
        return ArrayHelper::map(MFishTypes::find()->orderBy("name")->asArray()->all(), 'id', "name");
    }

    public static function getZoneArray(): array
    {
        return ArrayHelper::map(MZone::find()->asArray()->all(), 'id', "zone");
    }

    public static function getExpireDate($months, $createDate = null)
    {
        $date = date('Y-m-d', strtotime('+' . strval($months) . ' month', strtotime($createDate == null ? date('Y-m-d') : $createDate)));
        if ($createDate == null) {
            $date = date('Y-m-d', strtotime('-1 days', strtotime($date)));
        }
        return $date;
    }


    /**
     * @param BoatNumbers $boat
     * @return void
     */
    public static function markAsPaid($model, $processType)
    {
        $approvalWorkflow = MApprovalWorkflow::findOne(['type' => $processType]);

        $model->status = 101;
        $model->approval_stage = "Completed";
        $model->approved_time = date("Y-m-d H:i");
        $model->expire_date = CommonService::getExpireDate($approvalWorkflow->expired_in, self::getRenewLicenseExpireDate($model, $processType));
    }

  public static function getDateDiffWithCurrentDate($date): ?int
{
    if ($date === null || trim((string) $date) === '') {
        return null;
    }

    $timestamp = strtotime((string) $date);

    if ($timestamp === false) {
        return null;
    }

    return (int) round(
        ($timestamp - time()) / 86400
    );
}

    public static function generateQRCode($content)
    {
        $qrCode = (new QrCode($content))
            ->setSize(250)
            ->setMargin(5);
        return base64_encode($qrCode->writeString());
        /*        <img src="data:image/png;base64,<?=$qr_image?> " />*/

    }

    public static function getApprovedOfficer($processId, $processType)
    {
        $model = ApprovalLog::find()->where(["type" => $processType, "process_id" => $processId, "status" => "approve"])->orderBy(["id" => SORT_DESC])->one();
        if ($model != "") {
            $user = User::findOne($model->done_by);
            $officer = ProfileOfficer::find()->where(["id" => $user->profile_id])->asArray()->one();
            $officer["type"] = $user->type;

            return $officer;
        }
    }

    public static function getRenewLicenseExpireDate($model, $processType)
    {
        if (isset($model->renew) && $model->renew == 1) {
            if ($processType == "NATIONAL_LICENSE") {
                $previousLicense = NationalLicense::find()->where(["!=", "id", $model->id])->andWhere(["boat_registration_id" => $model->boat_registration_id])->orderBy(["id" => SORT_DESC])->one();
                return $previousLicense->expire_date;
            }
            if ($processType == "HIGHSEAS_LICENSE") {
                $previousLicense = HighseasLicense::find()->where(["!=", "id", $model->id])->andWhere(["boat_registration_id" => $model->boat_registration_id])->orderBy(["id" => SORT_DESC])->one();
                return $previousLicense->expire_date;
            }
            if ($processType == "HIGHSEAS_LICENSE_RENEW") {
                $previousLicense = HighseasLicense::find()->where(["!=", "id", $model->id])->andWhere(["boat_registration_id" => $model->boat_registration_id])->orderBy(["id" => SORT_DESC])->one();
                return $previousLicense->expire_date;
            }
            if ($processType == "BOAT_REGISTER_ReNEW") {
                $previousLicense = FishermanRegisterdBoatLicense::find()->where(["=", "id", $model->id])->orderBy(["nid" => SORT_DESC])->one();
                return $previousLicense->expire_date;
            }
        }

        return null;
    }

    public static function validateApprovePermission($district, $division, $stage): bool
    {
        if (UserTypeUtil::hasType(Constant::FI) && Yii::$app->session->get("officer_division") == $division && $stage
            == Constant::FI) {
            return true;
        }
        if (UserTypeUtil::hasType(Constant::AD) && Yii::$app->session->get("officer_district") == $district && $stage
            == Constant::AD) {
            return true;
        }
        if (UserTypeUtil::hasType(Constant::DFI) && Yii::$app->session->get("officer_district") == $district &&
            $stage == Constant::DFI) {
            return true;
        }
        if (UserTypeUtil::hasType(Constant::DO) && Yii::$app->session->get("officer_district") == $district && $stage
            == Constant::DO) {
            return true;
        }

        return false;
    }

    public static function getCountryNames($countryCodes)
    {
        $countries = Constant::$countries;

        // Split the comma-separated country codes
        $codes = explode(',', $countryCodes);
        $names = [];

        // Map codes to names
        foreach ($codes as $code) {
            $code = trim($code); // Remove any whitespace
            if (isset($countries[$code])) {
                $names[] = $countries[$code];
            } else {
                $names[] = "Unknown ($code)"; // Handle missing codes
            }
        }

        // Return as a string or array based on your needs
        return implode(', ', $names); // e.g., "Cook Islands, Costa Rica"
    }

    public static function numberToWords($number)
    {
        $ones = array(
            0 => "zero", 1 => "one", 2 => "two", 3 => "three", 4 => "four",
            5 => "five", 6 => "six", 7 => "seven", 8 => "eight", 9 => "nine",
            10 => "ten", 11 => "eleven", 12 => "twelve", 13 => "thirteen",
            14 => "fourteen", 15 => "fifteen", 16 => "sixteen",
            17 => "seventeen", 18 => "eighteen", 19 => "nineteen"
        );

        $tens = array(
            2 => "twenty", 3 => "thirty", 4 => "forty", 5 => "fifty",
            6 => "sixty", 7 => "seventy", 8 => "eighty", 9 => "ninety"
        );

        $thousands = array(
            0 => "", 1 => "thousand", 2 => "million", 3 => "billion"
        );

        if ($number == 0) {
            return $ones[0];
        }

        $words = "";
        $group = 0;

        while ($number > 0) {
            $chunk = $number % 1000;
            if ($chunk > 0) {
                $chunkWords = "";

                // Handle hundreds
                if ($chunk >= 100) {
                    $chunkWords .= $ones[floor($chunk / 100)] . " hundred";
                    $chunk %= 100;
                    if ($chunk > 0) {
                        $chunkWords .= " and ";
                    }
                }

                // Handle tens and ones
                if ($chunk >= 20) {
                    $chunkWords .= $tens[floor($chunk / 10)];
                    $chunk %= 10;
                    if ($chunk > 0) {
                        $chunkWords .= " " . $ones[$chunk];
                    }
                } elseif ($chunk > 0) {
                    $chunkWords .= $ones[$chunk];
                }

                // Add thousand, million, etc.
                if ($chunkWords != "") {
                    $words = $chunkWords . " " . $thousands[$group] . ($words ? " " . $words : "");
                }
            }

            $number = floor($number / 1000);
            $group++;
        }

        return trim($words);
    }


    public static function validateLicenseType(ExportCompany $model, $process)
    {
        $types = explode(',', $model->appication_types);
        if (!in_array((string)$process, $types)) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You dont have permission for this license type.'));

        }
    }
}