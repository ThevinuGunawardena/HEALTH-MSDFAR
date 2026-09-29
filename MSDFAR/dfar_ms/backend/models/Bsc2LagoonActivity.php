<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bsc2_lagoon_activities".
 *
 * Lagoon management activities — a repeatable free-form list per
 * submission, same pattern as Bsc1AwarenessProgramme.
 *
 * @property int $id
 * @property int $submission_id
 * @property string|null $lagoon_name
 * @property string|null $activity
 *
 * @property Bsc2Submission $submission
 */
class Bsc2LagoonActivity extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc2_lagoon_activities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['submission_id'], 'required'],
            [['submission_id'], 'integer'],
            [['lagoon_name'], 'string', 'max' => 150],
            [['activity'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'submission_id' => Yii::t('app', 'Submission'),
            'lagoon_name' => Yii::t('app', 'Name of the Lagoon'),
            'activity' => Yii::t('app', 'Special Activities Carried Out'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmission()
    {
        return $this->hasOne(Bsc2Submission::class, ['id' => 'submission_id']);
    }
}