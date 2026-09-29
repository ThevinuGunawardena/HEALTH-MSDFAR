<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bsc1_awareness_programmes".
 *
 * Awareness programmes / special events — a repeatable free-form list per
 * submission. Unlike the other three child tables, this one is a genuine
 * list (add/remove any number of rows), not a fixed set — see
 * BscFormsController::saveBsc1ChildRecords() for how new/updated/deleted
 * rows are distinguished on save.
 *
 * @property int $id
 * @property int $submission_id
 * @property string|null $event_date
 * @property string|null $nature
 * @property int|null $participants
 * @property float|null $cost
 * @property string|null $resource_person
 * @property string|null $institution
 *
 * @property Bsc1Submission $submission
 */
class Bsc1AwarenessProgramme extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc1_awareness_programmes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['submission_id'], 'required'],
            [['submission_id'], 'integer'],
            [['event_date'], 'date', 'format' => 'php:Y-m-d'],
            [['nature'], 'string', 'max' => 255],
            [['participants'], 'integer', 'min' => 0],
            [['participants'], 'default', 'value' => 0],
            [['cost'], 'number', 'min' => 0],
            [['cost'], 'default', 'value' => 0],
            [['resource_person', 'institution'], 'string', 'max' => 150],
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
            'event_date' => Yii::t('app', 'Date'),
            'nature' => Yii::t('app', 'Nature of Programme'),
            'participants' => Yii::t('app', 'Participants'),
            'cost' => Yii::t('app', 'Cost (Rs.)'),
            'resource_person' => Yii::t('app', 'Resource Person'),
            'institution' => Yii::t('app', 'Institution'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmission()
    {
        return $this->hasOne(Bsc1Submission::class, ['id' => 'submission_id']);
    }
}