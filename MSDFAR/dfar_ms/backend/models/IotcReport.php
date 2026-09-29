<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "iotc_report".
 *
 * @property string|null $first_name
 * @property string|null $preferred_name_for_id
 * @property string|null $nic
 * @property string|null $permanent_address
 * @property string|null $current_address
 * @property string|null $boat_number
 * @property int|null $hull_number
 * @property string|null $main_gear_types
 * @property string|null $sub_gear_types
 * @property string|null $license_number
 * @property string|null $call_sign_no
 * @property string|null $date_of_construction
 * @property string|null $engine_make
 * @property string $engine_name
 * @property string|null $approved_time
 * @property string|null $expire_date
 * @property string|null $description
 * @property float|null $length
 * @property float|null $width
 * @property float|null $height
 * @property string|null $Harbour_Name
 * @property string|null $yard_name
 * @property string|null $from_date
 */
class IotcReport extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iotc_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'preferred_name_for_id', 'nic', 'permanent_address', 'current_address', 'boat_number', 'hull_number', 'main_gear_types', 'sub_gear_types', 'license_number', 'call_sign_no', 'date_of_construction', 'engine_make', 'approved_time', 'expire_date', 'description', 'length', 'width', 'height', 'Harbour_Name', 'yard_name', 'from_date'], 'default', 'value' => null],
            [['engine_name'], 'default', 'value' => ''],
            [['hull_number'], 'integer'],
            [['date_of_construction', 'approved_time', 'expire_date', 'from_date'], 'safe'],
            [['length', 'width', 'height'], 'number'],
            [['first_name', 'preferred_name_for_id', 'sub_gear_types', 'description', 'yard_name'], 'string', 'max' => 200],
            [['nic', 'boat_number', 'main_gear_types', 'license_number', 'call_sign_no'], 'string', 'max' => 100],
            [['permanent_address', 'current_address'], 'string', 'max' => 500],
            [['engine_make'], 'string', 'max' => 11],
            [['engine_name'], 'string', 'max' => 8],
            [['Harbour_Name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'first_name' => 'First Name',
            'preferred_name_for_id' => 'Preferred Name For ID',
            'nic' => 'Nic',
            'permanent_address' => 'Permanent Address',
            'current_address' => 'Current Address',
            'boat_number' => 'Boat Number',
            'hull_number' => 'Hull Number',
            'main_gear_types' => 'Main Gear Types',
            'sub_gear_types' => 'Sub Gear Types',
            'license_number' => 'License Number',
            'call_sign_no' => 'Call Sign No',
            'date_of_construction' => 'Date Of Construction',
            'engine_make' => 'Engine Make',
            'engine_name' => 'Engine Name',
            'approved_time' => 'Approved Time',
            'expire_date' => 'Expire Date',
            'description' => 'Description',
            'length' => 'Length',
            'width' => 'Width',
            'height' => 'Height',
            'Harbour_Name' => 'Harbour Name',
            'yard_name' => 'Yard Name',
            'from_date' => 'From Date',
        ];
    }

}
