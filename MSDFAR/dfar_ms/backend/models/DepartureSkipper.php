<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "departure_skipper".
 *
 * @property int $id
 * @property string $skipper_name
 * @property string $crew_type
 * @property string $nic
 * @property string $skipper_id
 * @property string $address
 * @property string $contact
 * @property string $status
 * @property string $approved_by
 * @property string $timestamp
 * @property string $harbor
 * @property string|null $served_vessel
 * @property string|null $dep_date
 * @property string $dep_id
 * @property string $dep_cancel_allow_by
 * @property string $dep_cancel_date
 * @property string $to_date
 * @property string $remarks
 * @property string $offence_reason
 */
class DepartureSkipper extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'departure_skipper';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['skipper_name', 'crew_type', 'nic', 'address', 'contact', 'status', 'approved_by', 'timestamp', 'harbor', 'dep_id', 'dep_cancel_allow_by', 'dep_cancel_date', 'to_date', 'remarks', 'offence_reason'], 'required'],
            [['timestamp', 'dep_date', 'dep_cancel_date', 'skipper_id'], 'safe'],
            [['to_date'], 'string'],
            [['skipper_name', 'crew_type'], 'string', 'max' => 200],
            [['nic'], 'string', 'max' => 12],
            [['skipper_id', 'dep_id'], 'string', 'max' => 20],
            [['address', 'approved_by', 'dep_cancel_allow_by', 'remarks', 'offence_reason'], 'string', 'max' => 225],
            [['contact'], 'string', 'max' => 22],
            [['status', 'harbor'], 'string', 'max' => 50],
            [['served_vessel'], 'string', 'max' => 25],
            [['nic'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'skipper_name' => Yii::t('app', 'Skipper Name'),
            'crew_type' => Yii::t('app', 'Crew Type'),
            'nic' => Yii::t('app', 'Nic'),
            'skipper_id' => Yii::t('app', 'Skipper ID'),
            'address' => Yii::t('app', 'Address'),
            'contact' => Yii::t('app', 'Contact'),
            'status' => Yii::t('app', 'Status'),
            'approved_by' => Yii::t('app', 'Approved By'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'harbor' => Yii::t('app', 'Harbor'),
            'served_vessel' => Yii::t('app', 'Served Vessel'),
            'dep_date' => Yii::t('app', 'Departure Date'),
            'dep_id' => Yii::t('app', 'Departure ID'),
            'dep_cancel_allow_by' => Yii::t('app', 'Dep Cancel Allow By'),
            'dep_cancel_date' => Yii::t('app', 'Applicable from'),
            'to_date' => Yii::t('app', 'Applicable To'),
            'remarks' => Yii::t('app', 'Remarks'),
            'offence_reason' => Yii::t('app', 'Offence / Reason'),
        ];
    }
}
