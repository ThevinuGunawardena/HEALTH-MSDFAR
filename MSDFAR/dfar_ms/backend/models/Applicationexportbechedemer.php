<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationexportbechedemer".
 *
 * @property int $id
 * @property string $full_name
 * @property string $address
 * @property string $telephone_number
 * @property int $fax_number
 * @property string $email
 * @property int $company
 * @property string $business_reg_number
 * @property float|null $charges
 * @property string|null $export_country
 * @property string $additional_information
 * @property string|null $export_countries
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationexportbechedemer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationexportbechedemer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'address', 'telephone_number', 'fax_number', 'email', 'company', 'business_reg_number', 'status', 'approval_stage', 'export_countries', 'export_quantity_under_previous_license'], 'required'],
            [['fax_number', 'company', 'status'], 'integer'],
            [['charges'], 'number'],
            [['additional_information', 'tnc', 'file_number'], 'string'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'address', 'email'], 'string', 'max' => 100],
            [['telephone_number'], 'string', 'max' => 12],
            [['business_reg_number', 'approval_stage'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'full_name' => Yii::t('app', "Applicants/Institution's Full Name "),
            'address' => Yii::t('app', 'Address'),
            'telephone_number' => Yii::t('app', 'Telephone Number'),
            'fax_number' => Yii::t('app', 'Fax Number'),
            'email' => Yii::t('app', 'Email'),
            'company' => Yii::t('app', 'Company'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'charges' => Yii::t('app', 'Charge to be paid'),
            'export_country' => Yii::t('app', '9Country of Export'),
            'additional_information' => Yii::t('app', 'Additional Information'),
            'export_countries' => Yii::t('app', 'Export Countries'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
