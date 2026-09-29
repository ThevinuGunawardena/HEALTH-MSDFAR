<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fisheries_progress".
 *
 * @property int $id
 * @property string $month
 * @property int $district
 * @property int $officer
 * @property string $timestamp
 *
 * @property FisheriesProgressRecords[] $fisheriesProgressRecords
 */
class FisheriesProgress extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisheries_progress';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['month', 'district', 'officer', 'timestamp'], 'required'],
            [['month', 'timestamp'], 'safe'],
            [['district', 'officer'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'month' => Yii::t('app', 'Month'),
            'district' => Yii::t('app', 'District'),
            'officer' => Yii::t('app', 'Officer'),
            'timestamp' => Yii::t('app', 'Timestamp'),
        ];
    }

    /**
     * Gets query for [[FisheriesProgressRecords]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFisheriesProgressRecords()
    {
        return $this->hasMany(FisheriesProgressRecords::class, ['fisheries_progress_id' => 'id']);
    }

}
