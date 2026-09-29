<?php

namespace backend\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "export_lobster_types".
 *
 * @property int $id
 * @property int $application_id
 * @property string $lobster_species
 * @property float|null $number
 * @property int $weight
 * @property string $caught_from
 */
class ExportLobsterTypes extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'export_lobster_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'default', 'value' => null],
            [['application_id', 'lobster_species', 'weight', 'caught_from'], 'required'],
            [['application_id', 'weight'], 'integer'],
            [['number'], 'number'],
            [['lobster_species', 'caught_from'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'application_id' => 'Application ID',
            'lobster_species' => 'Lobster Species',
            'number' => 'Number',
            'weight' => 'Weight',
            'caught_from' => 'Caught From',
        ];
    }

}
