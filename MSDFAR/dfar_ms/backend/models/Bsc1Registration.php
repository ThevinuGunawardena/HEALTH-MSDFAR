<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bsc1_registrations".
 *
 * Boat registration activity: 1st registration / renewal / cancellation,
 * each by craft type.
 *
 * @property int $id
 * @property int $submission_id
 * @property string $action first, renewal, cancellation
 * @property string $craft_type imul_over50, imul, iday, ofrp, mtrb, ntrb, nbsb
 * @property int $reg_count
 *
 * @property Bsc1Submission $submission
 */
class Bsc1Registration extends ActiveRecord
{
    const CRAFT_TYPES = ['imul_over50', 'imul', 'iday', 'ofrp', 'mtrb', 'ntrb', 'nbsb'];
    const ACTIONS = ['first', 'renewal', 'cancellation'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc1_registrations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['submission_id', 'action', 'craft_type'], 'required'],
            [['submission_id'], 'integer'],
            [['action'], 'in', 'range' => self::ACTIONS],
            [['craft_type'], 'in', 'range' => self::CRAFT_TYPES],
            [['reg_count'], 'integer', 'min' => 0],
            [['reg_count'], 'default', 'value' => 0],
            [['submission_id', 'action', 'craft_type'], 'unique', 'targetAttribute' => ['submission_id', 'action', 'craft_type']],
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
            'action' => Yii::t('app', 'Action'),
            'craft_type' => Yii::t('app', 'Craft Type'),
            'reg_count' => Yii::t('app', 'Registration Count'),
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