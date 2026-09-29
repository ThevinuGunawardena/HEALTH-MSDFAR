<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fisherman_impersonate_log".
 *
 * @property int $id
 * @property int $fisherman_id
 * @property int $impersonated_by
 * @property string $date_time
 * @property string $status
 */
class FishermanImpersonateLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisherman_impersonate_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fisherman_id', 'impersonated_by', 'date_time', 'status'], 'required'],
            [['fisherman_id', 'impersonated_by'], 'integer'],
            [['date_time'], 'safe'],
            [['status'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fisherman_id' => Yii::t('app', 'Fisherman ID'),
            'impersonated_by' => Yii::t('app', 'Impersonated By'),
            'date_time' => Yii::t('app', 'Date Time'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
