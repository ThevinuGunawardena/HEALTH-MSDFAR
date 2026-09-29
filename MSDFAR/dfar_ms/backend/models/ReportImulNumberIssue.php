<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_imul_number_issue".
 *
 * @property string|null $boat_registration_number
 * @property string $boat_owner_name
 * @property string $nic
 * @property string $address
 * @property string $owner_contact_details
 * @property string|null $name
 * @property string|null $yard_number
 * @property float|null $length
 * @property string|null $registration_number_issue_date
 */
class ReportImulNumberIssue extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_imul_number_issue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_owner_name', 'nic', 'address', 'owner_contact_details'], 'required'],
            [['length'], 'number'],
            [['registration_number_issue_date'], 'safe'],
            [['boat_registration_number', 'nic', 'yard_number'], 'string', 'max' => 100],
            [['boat_owner_name', 'name'], 'string', 'max' => 200],
            [['address'], 'string', 'max' => 500],
            [['owner_contact_details'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'boat_registration_number' => Yii::t('app', 'Boat Registration Number'),
            'boat_owner_name' => Yii::t('app', 'Boat Owner Name'),
            'nic' => Yii::t('app', 'Nic'),
            'address' => Yii::t('app', 'Address'),
            'owner_contact_details' => Yii::t('app', 'Owner Contact Details'),
            'name' => Yii::t('app', 'Name'),
            'yard_number' => Yii::t('app', 'Yard Number'),
            'length' => Yii::t('app', 'Length'),
            'registration_number_issue_date' => Yii::t('app', 'Registration Number Issue Date'),
        ];
    }
}

/*
CREATE OR REPLACE VIEW report_imul_number_issue AS SELECT bn.boat_number as boat_registration_number, fm.preferred_name_for_id as boat_owner_name, fm.nic, fm.permanent_address as address, fm.mobile as owner_contact_details, y.name, y.yard_uid as yard_number, bn.length,  bn.approved_time as registration_number_issue_date FROM `boat_numbers` bn
INNER JOIN profile_fisherman fm on bn.owner=fm.id
INNER JOIN profile_yard y on bn.yard=y.id
WHERE bn.boat_type=1 and bn.status =101 and bn.approval_stage="Completed";

*/