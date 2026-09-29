<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "boat_reg".
 *
 * @property int $id
 * @property string $owner_name
 * @property string $nic
 * @property string $boat_no
 * @property string|null $email
 * @property string|null $status
 * @property string|null $approved_by
 * @property string|null $timestamp
 * @property string|null $harbor
 * @property string $district
 * @property string $date_violation
 * @property string $dep_cancelled_by
 * @property string $dep_cancel_date
 * @property string $remarks
 * @property string $offence
 * @property string|null $to_date
 * @property string|null $reg_book
 */
class BoatReg extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'boat_reg';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'status', 'approved_by', 'timestamp', 'harbor', 'to_date'], 'default', 'value' => null],
            [['reg_book'], 'default', 'value' => 0],
            [['owner_name', 'nic', 'boat_no', 'district', 'date_violation', 'dep_cancelled_by', 'dep_cancel_date', 'remarks', 'offence'], 'required'],
            [['timestamp', 'dep_cancel_date'], 'safe'],
            [['date_violation'], 'string'],
            [['owner_name', 'email', 'approved_by', 'harbor', 'dep_cancelled_by', 'remarks', 'offence'], 'string', 'max' => 225],
            [['nic', 'boat_no'], 'string', 'max' => 12],
            [['status'], 'string', 'max' => 40],
            [['district'], 'string', 'max' => 20],
            [['to_date'], 'string', 'max' => 200],
            [['reg_book'], 'string', 'max' => 2],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner_name' => 'Owner Name',
            'nic' => 'Nic',
            'boat_no' => 'Boat No',
            'email' => 'Email',
            'status' => 'Status',
            'approved_by' => 'Approved By',
            'timestamp' => 'Timestamp',
            'harbor' => 'Harbor',
            'district' => 'District',
            'date_violation' => 'Date Violation',
            'dep_cancelled_by' => 'Dep Cancelled By',
            'dep_cancel_date' => 'Dep Cancel Date',
            'remarks' => 'Remarks',
            'offence' => 'Offence',
            'to_date' => 'To Date',
            'reg_book' => 'Reg Book',
        ];
    }

}
