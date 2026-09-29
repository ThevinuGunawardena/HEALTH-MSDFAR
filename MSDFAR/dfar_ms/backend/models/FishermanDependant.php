<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fisherman-dependant".
 *
 * @property int $id
 * @property int $fisherman_id
 * @property int $type
 * @property string $name
 * @property string $nic
 * @property string $birthday
 *
 * @property Fisherman $fisherman
 * @property MDependantType $type0
 */
class FishermanDependant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisherman_dependant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fisherman_id', 'type', 'name', 'nic', 'birthday'], 'required'],
            [['fisherman_id', 'type'], 'integer'],
            [['birthday'], 'safe'],
            [['name'], 'string', 'max' => 500],
            [['nic'], 'string', 'max' => 50],
            [['fisherman_id'], 'exist', 'skipOnError' => true, 'targetClass' => Fisherman::class, 'targetAttribute' => ['fisherman_id' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => MDependantType::class, 'targetAttribute' => ['type' => 'id']],
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
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'nic' => Yii::t('app', 'Nic'),
            'birthday' => Yii::t('app', 'Birthday'),
        ];
    }

    /**
     * Gets query for [[Fisherman]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFisherman()
    {
        return $this->hasOne(Fisherman::class, ['id' => 'fisherman_id']);
    }

    /**
     * Gets query for [[Type0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType0()
    {
        return $this->hasOne(MDependantType::class, ['id' => 'type']);
    }
}
