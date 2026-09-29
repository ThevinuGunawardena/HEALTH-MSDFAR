<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "export_live_fish_monthly_statement".
 *
 * @property int $id
 * @property int $application_id
 * @property string $month
 * @property string $fish
 * @property float $local_collected_qty
 * @property float $local_breed_qty
 * @property float $imported_reexported_qty
 */
class ExportLiveFishMonthlyStatement extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'export_live_fish_monthly_statement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'month', 'fish', 'local_collected_qty', 'local_breed_qty', 'imported_reexported_qty'], 'required'],
            [['application_id'], 'integer'],
            [['local_collected_qty', 'local_breed_qty', 'imported_reexported_qty'], 'number'],
            [['month', 'fish'], 'string', 'max' => 100],
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
            'month' => Yii::t('app', 'Month'),
            'fish' => Yii::t('app', 'Fish'),
            'local_collected_qty' => Yii::t('app', 'Local Collected Qty'),
            'local_breed_qty' => Yii::t('app', 'Local Breed Qty'),
            'imported_reexported_qty' => Yii::t('app', 'Imported Reexported Qty'),
        ];
    }
}
