<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_boat_category".
 *
 * @property int $id
 * @property int $boat_type
 * @property string $code
 * @property int $status
 *
 * @property MBoatTypes $boatType
 */
class MBoatCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_boat_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['boat_type', 'code'], 'required'],
            [['boat_type', 'status'], 'integer'],
            [['code'], 'string', 'max' => 100],
            [['boat_type'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatTypes::class, 'targetAttribute' => ['boat_type' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'boat_type' => Yii::t('app', 'Boat Type'),
            'code' => Yii::t('app', 'Code'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[BoatType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatType()
    {
        return $this->hasOne(MBoatTypes::class, ['id' => 'boat_type']);
    }
}
