<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "exportregistration".
 *
 * @property string $application_name
 * @property string $address
 * @property int $mobile
 * @property int $fax
 * @property string $email
 * @property int $reg_no
 * @property int $id
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
class Exportregistration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exportregistration';
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
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'application_name' => Yii::t('app', 'Application Name'),
            'address' => Yii::t('app', 'Address'),
            'mobile' => Yii::t('app', 'Mobile'),
            'fax' => Yii::t('app', 'Fax'),
            'email' => Yii::t('app', 'Email'),
            'reg_no' => Yii::t('app', 'Reg No'),
            'id' => Yii::t('app', 'ID'),
            'permit_type' => Yii::t('app', 'Permit Type'),
            'commercial_name' => Yii::t('app', 'Commercial Name'),
            'quantity_unit' => Yii::t('app', 'Quantity Unit'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'total_number' => Yii::t('app', 'Total Number'),
            'area' => Yii::t('app', 'Area'),
            'charge' => Yii::t('app', 'Charge'),
            'country_export' => Yii::t('app', 'Country Export'),
            'information' => Yii::t('app', 'Information'),
            'document' => Yii::t('app', 'Document'),
        ];
    }
}
