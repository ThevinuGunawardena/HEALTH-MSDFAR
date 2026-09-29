<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scientific_sampling_operation_cost".
 *
 * @property int $id
 * @property float|null $fuel_qty
 * @property float|null $fuel_price
 * @property float|null $ice_qty
 * @property float|null $ice_price
 * @property float|null $bait_qty
 * @property float|null $bait_price
 * @property float|null $labour_cost
 * @property float|null $food_water
 * @property float|null $other
 * @property string|null $remark
 * @property int $sampling_data_id
 *
 * @property ScientificSamplingOperationUtilization[] $scientificSamplingOperationUtilizations
 */
class ScientificSamplingOperationCost extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_operation_cost';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fuel_qty', 'fuel_price', 'ice_qty', 'ice_price', 'bait_qty', 'bait_price', 'labour_cost', 'food_water', 'other'], 'number'],
            [['sampling_data_id'], 'required'],
            [['sampling_data_id'], 'integer'],
            [['remark'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fuel_qty' => Yii::t('app', 'Fuel Qty'),
            'fuel_price' => Yii::t('app', 'Fuel Price'),
            'ice_qty' => Yii::t('app', 'Ice Qty'),
            'ice_price' => Yii::t('app', 'Ice Price'),
            'bait_qty' => Yii::t('app', 'Bait Qty'),
            'bait_price' => Yii::t('app', 'Bait Price'),
            'labour_cost' => Yii::t('app', 'Labour Cost'),
            'food_water' => Yii::t('app', 'Food Water'),
            'other' => Yii::t('app', 'Other'),
            'remark' => Yii::t('app', 'Remark'),
            'sampling_data_id' => Yii::t('app', 'Sampling Data ID'),
        ];
    }

    /**
     * Gets query for [[ScientificSamplingOperationUtilizations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScientificSamplingOperationUtilizations()
    {
        return $this->hasMany(ScientificSamplingOperationUtilization::class, ['operation_cost_id' => 'id']);
    }
}
