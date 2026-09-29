<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "naklaexport".
 *
 * @property string $full_name
 * @property string $permanent_address
 * @property int $telephone_number
 * @property int $fax_number
 * @property string $nic_number
 * @property int $business_reg_number
 * @property string $purchase_place
 * @property int $previouspermit_exported_quantity
 * @property string $export_countries
 * @property int $export_quantity
 */
class Naklaexport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'naklaexport';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'telephone_number', 'fax_number', 'nic_number', 'business_reg_number', 'purchase_place', 'previouspermit_exported_quantity', 'export_countries', 'export_quantity'], 'required'],
            [['telephone_number', 'fax_number', 'business_reg_number', 'previouspermit_exported_quantity', 'export_quantity'], 'integer'],
            [['full_name', 'permanent_address'], 'string', 'max' => 100],
            [['nic_number'], 'string', 'max' => 20],
            [['purchase_place', 'export_countries'], 'string', 'max' => 50],
            [['business_reg_number'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'full_name' => Yii::t('app', 'Full Name'),
            'permanent_address' => Yii::t('app', 'Permanent Address'),
            'telephone_number' => Yii::t('app', 'Telephone Number'),
            'fax_number' => Yii::t('app', 'Fax Number'),
            'nic_number' => Yii::t('app', 'Nic Number'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'purchase_place' => Yii::t('app', 'Purchase Place'),
            'previouspermit_exported_quantity' => Yii::t('app', 'Previouspermit Exported Quantity'),
            'export_countries' => Yii::t('app', 'Export Countries'),
            'export_quantity' => Yii::t('app', 'Export Quantity'),
        ];
    }
}
