<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationexportnakla".
 *
 * @property string $full_name
 * @property string $permanent_address
 * @property int $telephone_number
 * @property int $fax_number
 * @property string $nic_number
 * @property int $id
 * @property int $company
 * @property int $business_reg_number
 * @property string $purchase_place
 * @property int $previouspermit_exported_quantity_pieces
 * @property string $export_countries
 * @property int $export_quantity_pieces
 * @property int|null $previouspermit_exported_quantity_kg
 * @property int|null $export_quantity_kg
 * @property string|null $contact_value
 * @property int|null $contact_number
 * @property int|null $previouspermit_exported_quantity
 * @property int|null $export_quantity

 * @property string|null $request_date
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationexportnakla extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationexportnakla';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'permanent_address', 'telephone_number', 'fax_number', 'nic_number', 'company', 'business_reg_number', 'purchase_place', 'previouspermit_exported_quantity_pieces', 'export_countries', 'export_quantity_pieces', 'status', 'approval_stage'], 'required'],
            [['telephone_number', 'fax_number', 'company', 'business_reg_number', 'previouspermit_exported_quantity_pieces', 'export_quantity_pieces', 'previouspermit_exported_quantity_kg', 'export_quantity_kg', 'contact_number', 'previouspermit_exported_quantity', 'export_quantity', 'status'], 'integer'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'permanent_address'], 'string', 'max' => 100],
            [['nic_number'], 'string', 'max' => 20],
            [['purchase_place', 'approval_stage'], 'string', 'max' => 50],
            [['contact_value'], 'string', 'max' => 255],
            [['tnc'], 'string'],
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
            'id' => Yii::t('app', 'ID'),
            'company' => Yii::t('app', 'Company'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'purchase_place' => Yii::t('app', 'Purchase Place'),
            'previouspermit_exported_quantity_pieces' => Yii::t('app', 'Previouspermit Exported Quantity Pieces'),
            'export_countries' => Yii::t('app', 'Export Countries'),
            'export_quantity_pieces' => Yii::t('app', 'Export Quantity Pieces'),
            'previouspermit_exported_quantity_kg' => Yii::t('app', 'Previouspermit Exported Quantity Kg'),
            'export_quantity_kg' => Yii::t('app', 'Export Quantity Kg'),
            'contact_value' => Yii::t('app', 'Contact Value'),
            'contact_number' => Yii::t('app', 'Contact Number'),
            'previouspermit_exported_quantity' => Yii::t('app', 'Previouspermit Exported Quantity'),
            'export_quantity' => Yii::t('app', 'Export Quantity'),

            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
