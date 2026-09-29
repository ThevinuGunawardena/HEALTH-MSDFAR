<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationexportlobster".
 *
 * @property int $id
 * @property string $full_name
 * @property string $address
 * @property int|null $telephone_number
 * @property int $company
 * @property string|null $export_countries
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationexportlobster extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationexportlobster';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'address', 'company', 'status', 'approval_stage', 'export_countries'], 'required'],
            [['telephone_number', 'company', 'status'], 'integer'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'address', 'file_number'], 'string', 'max' => 100],
            [['approval_stage'], 'string', 'max' => 50],
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
            'telephone_number' => Yii::t('app', 'Telephone Number'),
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
