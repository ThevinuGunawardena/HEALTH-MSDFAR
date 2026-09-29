<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "scientific_sampling_operation_utilization".
 *
 * @property int $id
 * @property int $operation_cost_id
 * @property int $specie
 * @property float|null $export_qty
 * @property float|null $export_value
 * @property float|null $local_qty
 * @property float|null $local_value
 * @property float|null $dried_qty
 * @property float|null $dried_value
 * @property float|null $discard_qty
 * @property float|null $discard_value
 * @property int $trash
 * @property int|null $status
 *
 * @property ScientificSamplingOperationCost $operationCost
 * @property MFishTypes $specie0
 */
class ScientificSamplingOperationUtilization extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_sampling_operation_utilization';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['operation_cost_id', 'specie'], 'required'],
            [['operation_cost_id', 'specie', 'trash', 'status'], 'integer'],
            [['export_qty', 'export_value', 'local_qty', 'local_value', 'dried_qty', 'dried_value', 'discard_qty', 'discard_value'], 'number'],
            [['operation_cost_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScientificSamplingOperationCost::class, 'targetAttribute' => ['operation_cost_id' => 'id']],
            [['specie'], 'exist', 'skipOnError' => true, 'targetClass' => MFishTypes::class, 'targetAttribute' => ['specie' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'operation_cost_id' => Yii::t('app', 'Operation Cost ID'),
            'specie' => Yii::t('app', 'Specie'),
            'export_qty' => Yii::t('app', 'Export Qty'),
            'export_value' => Yii::t('app', 'Export Value'),
            'local_qty' => Yii::t('app', 'Local Qty'),
            'local_value' => Yii::t('app', 'Local Value'),
            'dried_qty' => Yii::t('app', 'Dried Qty'),
            'dried_value' => Yii::t('app', 'Dried Value'),
            'discard_qty' => Yii::t('app', 'Discard Qty'),
            'discard_value' => Yii::t('app', 'Discard Value'),
            'trash' => Yii::t('app', 'Trash'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[OperationCost]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOperationCost()
    {
        return $this->hasOne(ScientificSamplingOperationCost::class, ['id' => 'operation_cost_id']);
    }

    /**
     * Gets query for [[Specie0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecie0()
    {
        return $this->hasOne(MFishTypes::class, ['id' => 'specie']);
    }
}
