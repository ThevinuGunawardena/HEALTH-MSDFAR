<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "possessionregistration".
 *
 * @property string $applicant_name
 * @property string $address
 * @property int $mobile_number
 * @property string $email
 * @property string $nid_number
 * @property int $reg_number
 * @property string $mail_address
 * @property string $permit_type
 * @property string $type
 * @property int $quantity
 * @property string $area
 * @property string $vehicle_number
 * @property string $destination_district
 * @property string $product_storing_area
 * @property string $finalstoreplace_address
 * @property string $import_country
 * @property resource $document
 */
class Possessionregistration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'possessionregistration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['applicant_name', 'address', 'mobile_number', 'email', 'nid_number', 'reg_number', 'mail_address', 'permit_type', 'type', 'quantity', 'area', 'vehicle_number', 'destination_district', 'product_storing_area', 'finalstoreplace_address', 'import_country', 'document'], 'required'],
            [['mobile_number', 'reg_number', 'quantity'], 'integer'],
            [['document'], 'string'],
            [['applicant_name', 'nid_number', 'permit_type', 'type', 'area', 'vehicle_number', 'destination_district', 'product_storing_area', 'import_country'], 'string', 'max' => 20],
            [['address', 'email', 'mail_address', 'finalstoreplace_address'], 'string', 'max' => 50],
            [['nid_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'applicant_name' => Yii::t('app', 'Applicant Name'),
            'address' => Yii::t('app', 'Address'),
            'mobile_number' => Yii::t('app', 'Mobile Number'),
            'email' => Yii::t('app', 'Email'),
            'nid_number' => Yii::t('app', 'Nid Number'),
            'reg_number' => Yii::t('app', 'Reg Number'),
            'mail_address' => Yii::t('app', 'Mail Address'),
            'permit_type' => Yii::t('app', 'Permit Type'),
            'type' => Yii::t('app', 'Type'),
            'quantity' => Yii::t('app', 'Quantity'),
            'area' => Yii::t('app', 'Area'),
            'vehicle_number' => Yii::t('app', 'Vehicle Number'),
            'destination_district' => Yii::t('app', 'Destination District'),
            'product_storing_area' => Yii::t('app', 'Product Storing Area'),
            'finalstoreplace_address' => Yii::t('app', 'Finalstoreplace Address'),
            'import_country' => Yii::t('app', 'Import Country'),
            'document' => Yii::t('app', 'Document'),
        ];
    }
}
