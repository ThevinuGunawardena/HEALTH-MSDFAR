<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "m_boat_sub_category".
 *
 * @property int $id
 * @property string $code
 * @property string $description
 * @property int $status
 * @property int|null $boat_type_id
 *
 * @property MBoatTypes $boatType
 * @property ScientificFleetData[] $scientificFleetDatas
 * @property ScientificSamplingBoatGearData[] $scientificSamplingBoatGearDatas
 */
class MBoatSubCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_boat_sub_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code', 'description', 'status'], 'required'],
            [['status', 'boat_type_id'], 'integer'],
            [['code'], 'string', 'max' => 200],
            [['description'], 'string', 'max' => 500],
            [['boat_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => MBoatTypes::class, 'targetAttribute' => ['boat_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
            'boat_type_id' => Yii::t('app', 'Boat Type ID'),
        ];
    }

    /**
     * Gets query for [[BoatType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBoatType()
    {
        return $this->hasOne(MBoatTypes::class, ['id' => 'boat_type_id']);
    }

    /**
     * Gets query for [[ScientificFleetDatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScientificFleetDatas()
    {
        return $this->hasMany(ScientificFleetData::class, ['sub_category' => 'id']);
    }

    /**
     * Gets query for [[ScientificSamplingBoatGearDatas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScientificSamplingBoatGearDatas()
    {
        return $this->hasMany(ScientificSamplingBoatGearData::class, ['sub_category' => 'id']);
    }
}
