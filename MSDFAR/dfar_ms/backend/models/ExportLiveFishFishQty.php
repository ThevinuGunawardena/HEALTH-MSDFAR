<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "export_live_fish_fish_qty".
 *
 * @property int $id
 * @property int $application_id
 * @property string $species_fish
 * @property int $qty
 * @property string $area_capture
 */
class ExportLiveFishFishQty extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'export_live_fish_fish_qty';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'species_fish', 'qty', 'area_capture'], 'required'],
            [['application_id', 'qty'], 'integer'],
            [['species_fish'], 'string', 'max' => 100],
            [['area_capture'], 'string', 'max' => 200],
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
            'species_fish' => Yii::t('app', 'Species Fish'),
            'qty' => Yii::t('app', 'Qty'),
            'area_capture' => Yii::t('app', 'Area Capture'),
        ];
    }
}
