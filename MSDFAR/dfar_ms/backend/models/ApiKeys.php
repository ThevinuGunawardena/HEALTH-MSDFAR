<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "api_keys".
 *
 * @property int $id
 * @property int $client_id FK to api_clients.id
 * @property string|null $key_name Example: Production Key, Test Key
 * @property string $api_key_hash
 * @property int $status 1=Active, 0=Inactive
 * @property string|null $expires_at
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ApiKeys extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_keys';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key_name', 'expires_at', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 1],
            [['client_id', 'api_key_hash'], 'required'],
            [['client_id', 'status'], 'integer'],
            [['expires_at', 'created_at', 'updated_at'], 'safe'],
            [['key_name', 'api_key_hash'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'key_name' => Yii::t('app', 'Key Name'),
            'api_key_hash' => Yii::t('app', 'Api Key Hash'),
            'status' => Yii::t('app', 'Status'),
            'expires_at' => Yii::t('app', 'Expires At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

}
