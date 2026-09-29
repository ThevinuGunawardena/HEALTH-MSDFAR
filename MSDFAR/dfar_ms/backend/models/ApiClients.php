<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "api_clients".
 *
 * @property int $id
 * @property string $client_code Public client ID like TEST-01
 * @property string $client_name
 * @property string|null $contact_email
 * @property int $status 1=Active, 0=Inactive
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ApiClients extends ActiveRecord
{
    public static function tableName()
    {
        return 'api_clients';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('CURRENT_TIMESTAMP'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['contact_email'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],

            [['client_code', 'client_name'], 'required'],

            [['status'], 'integer'],

            [['created_at', 'updated_at'], 'safe'],

            [['client_code'], 'string', 'max' => 100],
            [['client_name', 'contact_email'], 'string', 'max' => 255],

            [['contact_email'], 'email'],

            [['client_code'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_code' => Yii::t('app', 'Client Code'),
            'client_name' => Yii::t('app', 'Client Name'),
            'contact_email' => Yii::t('app', 'Contact Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}