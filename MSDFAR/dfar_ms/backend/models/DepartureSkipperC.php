<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "departure_skipper".
 *
 * @property int $id
 * @property string $skipper_name
 * @property string $crew_type
 * @property string $nic
 * @property string|null $skipper_id
 * @property string $address
 * @property string $contact
 * @property string $status
 * @property string|null $approved_by
 * @property string|null $timestamp
 * @property string|null $harbor
 * @property string|null $served_vessel
 * @property string|null $dep_date
 * @property string|null $dep_id
 * @property string|null $dep_cancel_allow_by
 * @property string|null $dep_cancel_date
 * @property string|null $to_date
 * @property string|null $remarks
 * @property string|null $offence_reason
 */
class DepartureSkipperC extends ActiveRecord
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
            [['skipper_id', 'approved_by', 'timestamp', 'harbor', 'served_vessel', 'dep_date', 'dep_id', 'dep_cancel_allow_by', 'dep_cancel_date', 'to_date', 'remarks', 'offence_reason'], 'default', 'value' => null],
            [['skipper_name', 'crew_type', 'nic', 'address', 'contact', 'status'], 'required'],
            [['timestamp', 'dep_date', 'dep_cancel_date'], 'safe'],
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
            'id' => 'ID',
            'skipper_name' => 'Skipper Name',
            'crew_type' => 'Crew Type',
            'nic' => 'Nic',
            'skipper_id' => 'Skipper ID',
            'address' => 'Address',
            'contact' => 'Contact',
            'status' => 'Status',
            'approved_by' => 'Approved By',
            'timestamp' => 'Timestamp',
            'harbor' => 'Harbor',
            'served_vessel' => 'Served Vessel',
            'dep_date' => 'Dep Date',
            'dep_id' => 'Dep ID',
            'dep_cancel_allow_by' => 'Dep Cancel Allow By',
            'dep_cancel_date' => 'Dep Cancel Date',
            'to_date' => 'To Date',
            'remarks' => 'Remarks',
            'offence_reason' => 'Offence Reason',
        ];
    }

}
