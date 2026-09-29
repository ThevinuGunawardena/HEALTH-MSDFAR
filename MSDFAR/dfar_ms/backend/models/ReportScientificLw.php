<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "report_scientific_lw".
 *
 * @property int $scientifi_data_id
 * @property string $scientific_code
 * @property string $boat_number
 * @property string $name
 * @property int $id
 * @property int $sampling_data_id
 * @property int $gear
 * @property int $specie
 * @property int $specie_count
 * @property float $weight
 * @property int $weight_code
 * @property int $length_type
 * @property int $length_code
 * @property float $length
 * @property int $status
 * @property string $code
 * @property string $description
 */
class ReportScientificLw extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'report_scientific_lw';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scientifi_data_id', 'id', 'sampling_data_id', 'gear', 'specie', 'specie_count', 'weight_code', 'length_type', 'length_code', 'status'], 'integer'],
            [['boat_number', 'name', 'sampling_data_id', 'gear', 'specie', 'specie_count', 'weight', 'weight_code', 'length_type', 'length_code', 'length', 'status', 'code', 'description'], 'required'],
            [['weight', 'length'], 'number'],
            [['scientific_code'], 'safe'],
            [['scientific_code'], 'string', 'max' => 200],
            [['boat_number', 'name', 'description'], 'string', 'max' => 200],
            [['code'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scientifi_data_id' => Yii::t('app', 'Scientifi Data ID'),
            'scientific_code' => Yii::t('app', 'Scientific Code'),
            'boat_number' => Yii::t('app', 'Boat Number'),
            'name' => Yii::t('app', 'Name'),
            'id' => Yii::t('app', 'ID'),
            'sampling_data_id' => Yii::t('app', 'Sampling Data ID'),
            'gear' => Yii::t('app', 'Gear'),
            'specie' => Yii::t('app', 'Specie'),
            'specie_count' => Yii::t('app', 'Specie Count'),
            'weight' => Yii::t('app', 'Weight'),
            'weight_code' => Yii::t('app', 'Weight Code'),
            'length_type' => Yii::t('app', 'Length Type'),
            'length_code' => Yii::t('app', 'Length Code'),
            'length' => Yii::t('app', 'Length'),
            'status' => Yii::t('app', 'Status'),
            'code' => Yii::t('app', 'Code'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
}
