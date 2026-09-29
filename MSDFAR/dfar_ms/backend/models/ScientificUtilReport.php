<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "scientific_util_report".
 *
 * @property int $scientifi_data_id
 * @property string $start_time
 * @property int $added_by
 * @property string $boat_number
 * @property string $name
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
 */
class ScientificUtilReport extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scientific_util_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scientifi_data_id', 'added_by', 'id', 'operation_cost_id', 'specie', 'trash', 'status'], 'integer'],
            [['start_time', 'added_by', 'boat_number', 'name', 'operation_cost_id', 'specie'], 'required'],
            [['start_time'], 'safe'],
            [['export_qty', 'export_value', 'local_qty', 'local_value', 'dried_qty', 'dried_value', 'discard_qty', 'discard_value'], 'number'],
            [['boat_number', 'name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scientifi_data_id' => Yii::t('app', 'Scientifi Data ID'),
            'start_time' => Yii::t('app', 'Start Time'),
            'added_by' => Yii::t('app', 'Added By'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'name' => Yii::t('app', 'Name'),
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
}
/*
CREATE OR REPLACE VIEW scientific_util_report AS
SELECT s.id as scientifi_data_id,
start_time,added_by, sd.boat_number,ft.name, opu.*
 FROM `scientific_data` s
INNER JOIN scientific_sampling_data sd on sd.scientific_id=s.id
inner join scientific_sampling_operation_cost opc on opc.sampling_data_id=sd.id
inner join scientific_sampling_operation_utilization opu on opu.operation_cost_id=opc.id
inner join m_fish_types ft on opu.specie=ft.id
*/