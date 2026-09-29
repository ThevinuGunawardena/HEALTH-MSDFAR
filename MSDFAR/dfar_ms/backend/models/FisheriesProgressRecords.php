<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fisheries_progress_records".
 *
 * @property int $id
 * @property int $type
 * @property string $topic
 * @property string $description
 * @property int $fisheries_progress_id
 * @property int $officer_uid
 *
 * @property FisheriesProgress $fisheriesProgress
 */
class FisheriesProgressRecords extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fisheries_progress_records';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'topic', 'description', 'fisheries_progress_id', 'officer_uid'], 'required'],
            [['type', 'fisheries_progress_id', 'officer_uid'], 'integer'],
            [['topic', 'description'], 'string', 'max' => 255],
            [['fisheries_progress_id'], 'exist', 'skipOnError' => true, 'targetClass' => FisheriesProgress::class, 'targetAttribute' => ['fisheries_progress_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'topic' => Yii::t('app', 'Topic'),
            'description' => Yii::t('app', 'Description'),
            'fisheries_progress_id' => Yii::t('app', 'Fisheries Progress ID'),
            'officer_uid' => Yii::t('app', 'Officer Uid'),
        ];
    }

    /**
     * Gets query for [[FisheriesProgress]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFisheriesProgress()
    {
        return $this->hasOne(FisheriesProgress::class, ['id' => 'fisheries_progress_id']);
    }

}
