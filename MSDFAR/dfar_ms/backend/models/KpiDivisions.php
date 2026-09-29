<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kpi_divisions".
 *
 * @property int $divisionId
 * @property string $divisionName
 */
class KpiDivisions extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi_divisions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['divisionName'], 'required'],
            [['divisionName'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'divisionId' => Yii::t('app', 'Division ID'),
            'divisionName' => Yii::t('app', 'Division Name'),
        ];
    }
    
    /**
     * Gets query for [[ProfileOfficers]].
     * @return \yii\db\ActiveQuery
     */
    public function getProfileOfficers()
    {
        // Tracks relationship using your updated column name 'kpi_division_id'
        return $this->hasMany(ProfileOfficer::class, ['kpi_division_id' => 'divisionId']);
    }
}