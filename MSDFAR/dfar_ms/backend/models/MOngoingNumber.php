<?php

namespace backend\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "m_ongoing_number".
 *
 * @property int $id
 * @property int $type
 * @property string $letter
 * @property int $number
 *
 * @property MBoatTypes $type0
 */
class MOngoingNumber extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_ongoing_number';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'letter', 'number'], 'required'],
            [['type', 'number'], 'integer'],
            [['letter'], 'string', 'max' => 50],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatTypes::class, 'targetAttribute' => ['type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'letter' => Yii::t('app', 'Letter'),
            'number' => Yii::t('app', 'Number'),
        ];
    }

    /**
     * Gets query for [[Type0]].
     *
     * @return ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(MBoatTypes::class, ['id' => 'type']);
    }
}
