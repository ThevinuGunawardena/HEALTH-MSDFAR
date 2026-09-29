<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bsc2_seaworthiness_certs".
 *
 * Sea-worthiness certificates issued — Inboard/Outboard only. The
 * IMUL/IDAY/OFRP/MTRB craft-type breakdown belongs to boat insurance
 * instead — see Bsc2BoatInsured — per the updated paper form that
 * corrected the original (incorrect) merged interpretation.
 *
 * @property int $id
 * @property int $submission_id
 * @property string $category inboard, outboard
 * @property int $cert_count
 *
 * @property Bsc2Submission $submission
 */
class Bsc2SeaworthinessCertificates extends ActiveRecord
{
    const CATEGORIES = ['inboard', 'outboard'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bsc2_seaworthiness_certs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['submission_id', 'category'], 'required'],
            [['submission_id'], 'integer'],
            [['category'], 'in', 'range' => self::CATEGORIES],
            [['cert_count'], 'integer', 'min' => 0],
            [['cert_count'], 'default', 'value' => 0],
            [['submission_id', 'category'], 'unique', 'targetAttribute' => ['submission_id', 'category']],
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
            'category' => Yii::t('app', 'Category'),
            'cert_count' => Yii::t('app', 'Certificate Count'),
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