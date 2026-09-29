<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "api_key_permissions".
 *
 * @property int $id
 * @property int $api_key_id
 * @property int $api_endpoint_id
 * @property int $status 1=Allowed, 0=Blocked
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ApiKeyPermissions extends \yii\db\ActiveRecord
{
    // This is not a DB column.
    // This is used only for checkbox multiple API endpoint selection.
    public $api_endpoint_ids;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'api_key_permissions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['api_key_id'], 'required'],

            [['api_key_id', 'api_endpoint_id', 'status'], 'integer'],

            [['created_at', 'updated_at'], 'safe'],

            // Checkbox list field
            [['api_endpoint_ids'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'api_key_id' => Yii::t('app', 'API Key'),
            'api_endpoint_id' => Yii::t('app', 'API Endpoint'),
            'api_endpoint_ids' => Yii::t('app', 'Allowed APIs'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}