<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "blutracker_api_sync_status".
 *
 * @property int $Id
 * @property string $ApiName
 * @property int $LastMessageId
 * @property string|null $LastSyncTime
 * @property string $UpdatedAt
 */
class BlutrackerApiSyncStatus extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blutracker_api_sync_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['LastSyncTime'], 'default', 'value' => null],
            [['LastMessageId'], 'default', 'value' => 0],
            [['ApiName'], 'required'],
            [['LastMessageId'], 'integer'],
            [['LastSyncTime', 'UpdatedAt'], 'safe'],
            [['ApiName'], 'string', 'max' => 100],
            [['ApiName'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('app', 'ID'),
            'ApiName' => Yii::t('app', 'Api Name'),
            'LastMessageId' => Yii::t('app', 'Last Message ID'),
            'LastSyncTime' => Yii::t('app', 'Last Sync Time'),
            'UpdatedAt' => Yii::t('app', 'Updated At'),
        ];
    }

}
