<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_zone".
 *
 * @property int $id
 * @property string $zone
 * @property int $status
 */
class MZone extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_zone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['zone', 'status'], 'required'],
            [['status'], 'integer'],
            [['zone'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'zone' => Yii::t('app', 'Zone'),
            'status' => Yii::t('app', 'Status'),
        ];
    }
}
