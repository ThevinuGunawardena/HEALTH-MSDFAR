<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "exam_training".
 *
 * @property int $id
 * @property int $profile_officer_id
 * @property string $exam_training_name
 * @property string|null $exam_training_year
 * @property string|null $exam_training_institute
 * @property string|null $results_certificate
 */
class ExamTraining extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exam_training';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profile_officer_id', 'exam_training_name'], 'required'],
            [['profile_officer_id'], 'integer'],
            [['exam_training_year'], 'safe'],
            [['exam_training_name', 'exam_training_institute', 'results_certificate'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'profile_officer_id' => Yii::t('app', 'Profile Officer ID'),
            'exam_training_name' => Yii::t('app', 'Exam Training Name'),
            'exam_training_year' => Yii::t('app', 'Exam Training Year'),
            'exam_training_institute' => Yii::t('app', 'Exam Training Institute'),
            'results_certificate' => Yii::t('app', 'Results Certificate'),
        ];
    }
}
