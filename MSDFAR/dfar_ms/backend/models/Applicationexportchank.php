<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "applicationexportchank".
 *
 * @property string $full_name
 * @property string $address
 * @property string $telephone_number
 * @property int $id
 * @property int|null $company
 * @property string|null $export_countries
 * @property int $status
 * @property string $approval_stage
 * @property string $created
 * @property string|null $approved_time
 * @property string|null $expire_date
 */
class Applicationexportchank extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'applicationexportchank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['full_name', 'address', 'telephone_number', 'status', 'approval_stage', 'export_countries'], 'required'],
            [['company', 'status'], 'integer'],
            [['created', 'approved_time', 'expire_date'], 'safe'],
            [['full_name', 'address', 'file_number'], 'string', 'max' => 100],
            [['telephone_number', 'approval_stage'], 'string', 'max' => 50],
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
            'address' => Yii::t('app', 'Address'),
            'telephone_number' => Yii::t('app', 'Telephone Number'),
            'id' => Yii::t('app', 'ID'),
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
