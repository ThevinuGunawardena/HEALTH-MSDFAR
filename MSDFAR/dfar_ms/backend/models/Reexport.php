<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "reexport".
 *
 * @property string $applicant_name
 * @property string $permanent_address
 * @property string $email
 * @property int $id
 * @property int $business_reg_number
 * @property string $permit_type
 * @property string $commercial_name
 * @property float $total_weight
 * @property string $export_country
 * @property resource $document
 */
class Reexport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reexport';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['applicant_name', 'permanent_address', 'email', 'business_reg_number', 'permit_type', 'commercial_name', 'total_weight', 'export_country', 'document'], 'required'],
            [['business_reg_number'], 'integer'],
            [['total_weight'], 'number'],
            [['document'], 'string'],
            [['applicant_name', 'permit_type', 'commercial_name', 'export_country'], 'string', 'max' => 20],
            [['permanent_address', 'email'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'applicant_name' => Yii::t('app', 'Applicant Name'),
            'permanent_address' => Yii::t('app', 'Permanent Address'),
            'email' => Yii::t('app', 'Email'),
            'id' => Yii::t('app', 'ID'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'permit_type' => Yii::t('app', 'Permit Type'),
            'commercial_name' => Yii::t('app', 'Commercial Name'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'export_country' => Yii::t('app', 'Export Country'),
            'document' => Yii::t('app', 'Document'),
        ];
    }
}
