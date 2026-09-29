<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "api_endpoints".
 *
 * @property int $id
 * @property string $api_code
 * @property string $api_name
 * @property string $route
 * @property string $method
 * @property int $status 1=Active, 0=Inactive
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ApiEndpoints extends ActiveRecord
{
    public static function tableName()
    {
        return 'api_endpoints';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['method'], 'default', 'value' => 'GET'],
            [['status'], 'default', 'value' => 1],

            [['api_code', 'api_name', 'route'], 'required'],

            [['status'], 'integer'],

            [['created_at', 'updated_at'], 'safe'],

            [['api_code'], 'string', 'max' => 100],
            [['api_name'], 'string', 'max' => 255],
            [['route'], 'string', 'max' => 191],
            [['method'], 'string', 'max' => 10],

            [
                ['method'],
                'in',
                'range' => [
                    'GET',
                    'POST',
                    'PUT',
                    'PATCH',
                    'DELETE',
                    'OPTIONS',
                ],
            ],

            [['api_code'], 'unique'],

            [
                ['route', 'method'],
                'unique',
                'targetAttribute' => ['route', 'method'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'api_code' => Yii::t('app', 'API Code'),
            'api_name' => Yii::t('app', 'API Name'),
            'route' => Yii::t('app', 'Route'),
            'method' => Yii::t('app', 'Method'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}