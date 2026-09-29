<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hardware_repair".
 *
 * @property int $id
 * @property string|null $Name
 * @property int $Phone_number
 * @property string $Email
 * @property string $Office
 * @property string|null $Serial_number
 * @property string|null $Brand_name
 * @property string|null $Issue
 * @property string $Received_date
 * @property string $Status
 * @property string $Remarks
 */
class HardwareRepair extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hardware_repair';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Phone_number', 'Email', 'Office', 'Received_date', 'Status', 'Remarks'], 'required'],
            [['Phone_number'], 'integer'],
            [['Issue', 'Remarks'], 'string'],
            [['Received_date'], 'safe'],
            [['Name'], 'string', 'max' => 100],
            [['Email'], 'string', 'max' => 30],
            [['Office'], 'string', 'max' => 75],
            [['Serial_number', 'Brand_name', 'Status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Name' => 'Name',
            'Phone_number' => 'Phone_number',
            'Email' => 'Email',
            'Office' => 'Office',
            'Serial_number' => 'Serial Number',
            'Brand_name' => 'Brand Name',
            'Issue' => 'Issue',
            'Received_date' => 'Received Date',
            'Status' => 'Status',
            'Remarks' => 'Remarks',
        ];
    }
}
