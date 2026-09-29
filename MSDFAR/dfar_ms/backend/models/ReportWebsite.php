<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_website".
 *
 * @property string|null $boat_number
 * @property string $preferred_name_for_id
 * @property string $nic
 * @property string|null $national_license
 * @property string|null $nl_approved
 * @property string|null $nl_expired
 * @property string|null $highseas_license
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property string|null $call_sign_no
 * @property string|null $imo_no
 * @property string|null $ircs
 * @property string|null $iotc_record
 * @property string|null $mmsi_no_for_ais
 * @property string|null $log_book_no
 */
class ReportWebsite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_website5';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['preferred_name_for_id', 'nic'], 'required'],
            [['nl_approved', 'nl_expired', 'approved_time', 'expire_date'], 'safe'],
            [['boat_number', 'nic', 'national_license', 'highseas_license', 'call_sign_no', 'imo_no', 'ircs', 'iotc_record', 'mmsi_no_for_ais', 'log_book_no'], 'string', 'max' => 100],
            [['preferred_name_for_id'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'boat_number' => Yii::t('app', 'Boat Number'),
            'preferred_name_for_id' => Yii::t('app', 'Owner name'),
            'nic' => Yii::t('app', 'NIC'),
            'national_license_101' => Yii::t('app', 'EEZ operation license number'),
            'national_approved_101' => Yii::t('app', 'EEZ operation license valid from'),
            'national_expire_101' => Yii::t('app', 'EEZ operation license valid to'),
            'highseas_license_101' => Yii::t('app', 'High Seas operation license number '),
            'highseas_approved_101' => Yii::t('app', 'High Seas operation license valid from'),
            'highseas_expire_101' => Yii::t('app', 'High Seas operation license valid to'),
            'highseas_license_403' => Yii::t('app', 'Prev High Seas operation license number '),
            'highseas_expire_403' => Yii::t('app', 'Prev High Seas operation license valid to'),
            'national_license_403' => Yii::t('app', 'Prev EEZ operation license number '),
            'national_expire_403' => Yii::t('app', 'Prev EEZ operation license valid to'),
            'call_sign_no' => Yii::t('app', 'Call sign number'),
            'imo_no' => Yii::t('app', 'IMO number '),
            'iotc_record' => Yii::t('app', 'IOTC number'),
            'mmsi_no_for_ais' => Yii::t('app', 'AIS number'),
            'log_book_no' => Yii::t('app', 'Logbook number'),
        ];
    }
}

/*
CREATE OR REPLACE VIEW letest_bt_reg AS  SELECT btl1.*
    FROM fisherman_registerd_boat_license btl1
    INNER JOIN (
        SELECT id, MAX(nid) AS max_nid
        FROM fisherman_registerd_boat_license
        GROUP BY id
    ) btl2 ON btl1.id = btl2.id AND btl1.nid = btl2.max_nid;


 CREATE OR REPLACE VIEW report_website3 AS
SELECT
    bn.boat_number,
    f.preferred_name_for_id,
    f.nic,
    nl.license_number AS national_license,
    nl.approved_time AS nl_approved,
    nl.expire_date AS nl_expired,
    hl.license_number AS highseas_license,
    hl.approved_time,
    hl.expire_date,
    btl.call_sign_no,
    btl.imo_no,
    btl.ircs,
    btl.iotc_record,
    btl.mmsi_no_for_ais,
    btl.log_book_no
FROM dfar_ms.boat_numbers bn
INNER JOIN fisherman_registerd_boat bt ON bt.boat_number_id = bn.id
INNER JOIN profile_fisherman f ON f.id = bt.fisherman_id
LEFT JOIN national_license nl ON nl.boat_registration_id = bt.id AND nl.status = 101
LEFT JOIN highseas_license hl ON hl.boat_registration_id = bt.id AND hl.status = 101
LEFT JOIN letest_bt_reg btl ON btl.id = bt.id  -- Join latest license info
WHERE bn.boat_type = 1
AND bn.status = 101
AND (nl.id IS NOT NULL OR hl.id IS NOT NULL);


CREATE OR REPLACE VIEW report_website5 AS
SELECT
    bn.boat_number,
    f.preferred_name_for_id,
    f.nic,

    -- National License (current = max expire_date)
    nl_101.license_number AS national_license_101,
    nl_101.approved_time AS national_approved_101,
    nl_101.expire_date   AS national_expire_101,

    -- National License (previous = 2nd max expire_date)
    nl_403.license_number AS national_license_403,
    nl_403.approved_time AS national_approved_403,
    nl_403.expire_date   AS national_expire_403,

    -- High Seas License (current = max expire_date)
    hl_101.license_number AS highseas_license_101,
    hl_101.approved_time AS highseas_approved_101,
    hl_101.expire_date   AS highseas_expire_101,

    -- High Seas License (previous = 2nd max expire_date)
    hl_403.license_number AS highseas_license_403,
    hl_403.approved_time AS highseas_approved_403,
    hl_403.expire_date   AS highseas_expire_403,

    -- Boat details
    btl.call_sign_no,
    btl.imo_no,
    btl.ircs,
    btl.iotc_record,
    btl.mmsi_no_for_ais,
    btl.log_book_no

FROM dfar_ms.boat_numbers bn

INNER JOIN fisherman_registerd_boat bt
    ON bt.boat_number_id = bn.id

INNER JOIN profile_fisherman f
    ON f.id = bt.fisherman_id

-- 🔹 National current (latest expire_date)
LEFT JOIN (
    SELECT *
    FROM (
        SELECT *,
               ROW_NUMBER() OVER (
                   PARTITION BY boat_registration_id
                   ORDER BY expire_date DESC
               ) rn
        FROM national_license
        WHERE expire_date IS NOT NULL
    ) t
    WHERE rn = 1
) nl_101
    ON nl_101.boat_registration_id = bt.id

-- 🔹 National previous (2nd latest expire_date)
LEFT JOIN (
    SELECT *
    FROM (
        SELECT *,
               ROW_NUMBER() OVER (
                   PARTITION BY boat_registration_id
                   ORDER BY expire_date DESC
               ) rn
        FROM national_license
        WHERE expire_date IS NOT NULL
    ) t
    WHERE rn = 2
) nl_403
    ON nl_403.boat_registration_id = bt.id

-- 🔹 High Seas current (latest expire_date)
LEFT JOIN (
    SELECT *
    FROM (
        SELECT *,
               ROW_NUMBER() OVER (
                   PARTITION BY boat_registration_id
                   ORDER BY expire_date DESC
               ) rn
        FROM highseas_license
        WHERE expire_date IS NOT NULL
    ) t
    WHERE rn = 1
) hl_101
    ON hl_101.boat_registration_id = bt.id

-- 🔹 High Seas previous (2nd latest expire_date)
LEFT JOIN (
    SELECT *
    FROM (
        SELECT *,
               ROW_NUMBER() OVER (
                   PARTITION BY boat_registration_id
                   ORDER BY expire_date DESC
               ) rn
        FROM highseas_license
        WHERE expire_date IS NOT NULL
    ) t
    WHERE rn = 2
) hl_403
    ON hl_403.boat_registration_id = bt.id

LEFT JOIN letest_bt_reg btl
    ON btl.id = bt.id

WHERE bn.boat_type = 1
AND bn.status = 101
AND (
    nl_101.id IS NOT NULL
    OR hl_101.id IS NOT NULL
);
 */