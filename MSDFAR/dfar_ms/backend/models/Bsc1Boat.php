<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bsc1_boats".
 *
 * Existing operated boats — one row per craft type per submission.
 *
 * @property int $id
 * @property int $submission_id
 * @property string $craft_type imul_over50, imul, iday, ofrp, mtrb, ntrb, nbsb
 * @property int $boat_count
 *
 * @property Bsc1Submission $submission
 */
class Bsc1Boat extends ActiveRecord
{
    const CRAFT_TYPES = ['imul_over50', 'imul', 'iday', 'ofrp', 'mtrb', 'ntrb', 'nbsb'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc1_boats';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['submission_id', 'craft_type'], 'required'],
            [['submission_id'], 'integer'],
            [['craft_type'], 'in', 'range' => self::CRAFT_TYPES],
            [['boat_count'], 'integer', 'min' => 0],
            [['boat_count'], 'default', 'value' => 0],
            [['submission_id', 'craft_type'], 'unique', 'targetAttribute' => ['submission_id', 'craft_type']],
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
            'craft_type' => Yii::t('app', 'Craft Type'),
            'boat_count' => Yii::t('app', 'Boat Count'),
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