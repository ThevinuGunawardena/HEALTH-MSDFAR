<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bsc1_licenses_with_craft".
 *
 * Operating licenses issued with craft — one row per craft type per
 * submission. Note this craft-type set has no "imul_over50" category,
 * unlike Bsc1Boat / Bsc1Registration — matches LIC_WITH from the prototype.
 *
 * @property int $id
 * @property int $submission_id
 * @property string $craft_type imul, iday, ofrp, mtrb, ntrb, nbsb
 * @property int $license_count
 *
 * @property Bsc1Submission $submission
 */
class Bsc1LicenseWithCraft extends ActiveRecord
{
    const CRAFT_TYPES = ['imul', 'iday', 'ofrp', 'mtrb', 'ntrb', 'nbsb'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc1_licenses_with_craft';
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
            [['license_count'], 'integer', 'min' => 0],
            [['license_count'], 'default', 'value' => 0],
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
            'license_count' => Yii::t('app', 'License Count'),
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