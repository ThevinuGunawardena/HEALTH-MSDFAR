<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "application".
 *
 * @property string $application_name
 * @property string $address
 * @property int $mobile
 * @property int $fax
 * @property string $email
 * @property int $reg_no
 * @property string $permit_type
 * @property string $commercial_name
 * @property int $quantity_unit
 * @property float $total_weight
 * @property int $total_number
 * @property string $area
 * @property float $charge
 * @property string $country_export
 * @property string $information
 * @property resource $document
 */
class Application extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'application';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_name', 'address', 'mobile', 'fax', 'email', 'reg_no', 'permit_type', 'commercial_name', 'quantity_unit', 'total_weight', 'total_number', 'area', 'charge', 'country_export', 'information', 'document'], 'required'],
            [['mobile', 'fax', 'reg_no', 'quantity_unit', 'total_number'], 'integer'],
            [['total_weight', 'charge'], 'number'],
            [['information', 'document'], 'string'],
            [['application_name', 'permit_type', 'commercial_name', 'area', 'country_export'], 'string', 'max' => 20],
            [['address', 'email'], 'string', 'max' => 50],
            [['reg_no'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'application_name' => 'Application Name',
            'address' => 'Address',
            'mobile' => 'Mobile',
            'fax' => 'Fax',
            'email' => 'Email',
            'reg_no' => 'Reg No',
            'permit_type' => 'Permit Type',
            'commercial_name' => 'Commercial Name',
            'quantity_unit' => 'Quantity Unit',
            'total_weight' => 'Total Weight',
            'total_number' => 'Total Number',
            'area' => 'Area',
            'charge' => 'Charge',
            'country_export' => 'Country Export',
            'information' => 'Information',
            'document' => 'Document',
        ];
    }
}
