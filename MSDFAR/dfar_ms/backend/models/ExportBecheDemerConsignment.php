<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "export_beche_demer_consignment".
 *
 * @property int $id
 * @property int $application_id
 * @property string $commetial_name
 * @property float $quantity_per_unit
 * @property float $total_weight
 * @property float $total_number
 * @property string $supply_area
 */
class ExportBecheDemerConsignment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'export_beche_demer_consignment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'commetial_name', 'quantity_per_unit', 'total_weight', 'total_number', 'supply_area'], 'required'],
            [['application_id'], 'integer'],
            [['quantity_per_unit', 'total_weight', 'total_number'], 'number'],
            [['commetial_name', 'supply_area'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'application_id' => Yii::t('app', 'Application ID'),
            'commetial_name' => Yii::t('app', 'Commetial Name'),
            'quantity_per_unit' => Yii::t('app', 'Quantity Per Unit'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'total_number' => Yii::t('app', 'Total Number'),
            'supply_area' => Yii::t('app', 'Supply Area'),
        ];
    }
}
