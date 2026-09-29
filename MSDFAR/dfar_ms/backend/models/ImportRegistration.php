<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "importregistration".
 *
 * @property string $applicant_name
 * @property string $address
 * @property int $mobile_number
 * @property int $fixed_number
 * @property string $email
 * @property int $id
 * @property int $business_reg_no
 * @property string $permit_type
 * @property string $commercial_name
 * @property float $total_weight
 * @property string $imported_country
 * @property float $charge
 * @property string $information
 * @property resource $document
 */
class Importregistration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'importregistration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['applicant_name', 'address', 'mobile_number', 'fixed_number', 'email', 'business_reg_no', 'permit_type', 'commercial_name', 'total_weight', 'imported_country', 'charge', 'information', 'document'], 'required'],
            [['mobile_number', 'fixed_number', 'business_reg_no'], 'integer'],
            [['total_weight', 'charge'], 'number'],
            [['information', 'document'], 'string'],
            [['applicant_name', 'permit_type', 'commercial_name', 'imported_country'], 'string', 'max' => 20],
            [['address', 'email'], 'string', 'max' => 50],
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
            'fixed_number' => Yii::t('app', 'Fixed Number'),
            'email' => Yii::t('app', 'Email'),
            'id' => Yii::t('app', 'ID'),
            'business_reg_no' => Yii::t('app', 'Business Reg No'),
            'permit_type' => Yii::t('app', 'Permit Type'),
            'commercial_name' => Yii::t('app', 'Commercial Name'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'imported_country' => Yii::t('app', 'Imported Country'),
            'charge' => Yii::t('app', 'Charge'),
            'information' => Yii::t('app', 'Information'),
            'document' => Yii::t('app', 'Document'),
        ];
    }
}
