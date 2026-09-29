<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bsc2_boats_insured".
 *
 * "No of Fishing boat insured" — one row per craft type per submission.
 * New table, not part of the original design; added after an updated
 * paper form clarified this is a distinct concept from sea-worthiness
 * certificates, not a sub-category of it.
 *
 * @property int $id
 * @property int $submission_id
 * @property string $craft_type imul, iday, ofrp, mtrb
 * @property int $insured_count
 *
 * @property Bsc2Submission $submission
 */
class Bsc2BoatInsured extends ActiveRecord
{
    const CRAFT_TYPES = ['imul', 'iday', 'ofrp', 'mtrb'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc2_boats_insured';
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
            [['insured_count'], 'integer', 'min' => 0],
            [['insured_count'], 'default', 'value' => 0],
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
            'insured_count' => Yii::t('app', 'Boats Insured'),
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