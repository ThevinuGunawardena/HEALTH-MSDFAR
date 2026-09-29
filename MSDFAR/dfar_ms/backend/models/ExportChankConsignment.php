<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "export_chank_consignment".
 *
 * @property int $id
 * @property int $application_id
 * @property string $commetial_name
 * @property float $size
 * @property float $total_weight
 * @property string $caught_from
 */
class ExportChankConsignment extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'export_chank_consignment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['application_id', 'commetial_name', 'size', 'total_weight', 'caught_from'], 'required'],
            [['application_id'], 'integer'],
            [['size', 'total_weight'], 'number'],
            [['commetial_name', 'caught_from'], 'string', 'max' => 200],
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
            'size' => Yii::t('app', 'Size'),
            'total_weight' => Yii::t('app', 'Total Weight'),
            'caught_from' => Yii::t('app', 'Caught From'),
        ];
    }
}
