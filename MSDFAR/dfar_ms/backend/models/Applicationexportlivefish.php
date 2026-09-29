<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationexportlivefish".
 *
 * @property int $id
 * @property string $full_name
 * @property string $address
 * @property string $telephone
 * @property string $fax_number
 * @property int $business_reg_number
 * @property int $company
 * @property string $export_countries
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationexportlivefish extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationexportlivefish';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'address', 'telephone', 'fax_number', 'business_reg_number', 'company', 'export_countries', 'status', 'approval_stage'], 'required'],
            [['business_reg_number', 'company', 'status'], 'integer'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'address', 'export_countries'], 'string', 'max' => 100],
            [['telephone', 'fax_number', 'approval_stage'], 'string', 'max' => 50],
            [['tnc'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'full_name' => Yii::t('app', 'Full Name'),
            'address' => Yii::t('app', 'Address'),
            'telephone' => Yii::t('app', 'Telephone'),
            'fax_number' => Yii::t('app', 'Fax Number'),
            'business_reg_number' => Yii::t('app', 'Business Reg Number'),
            'company' => Yii::t('app', 'Company'),
            'export_countries' => Yii::t('app', 'Export Countries'),
            'status' => Yii::t('app', 'Status'),
            'approval_stage' => Yii::t('app', 'Approval Stage'),
            'created' => Yii::t('app', 'Created'),
            'approved_time' => Yii::t('app', 'Approved Time'),
            'expire_date' => Yii::t('app', 'Expire Date'),
        ];
    }
}
