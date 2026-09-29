<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "language_qualification".
 *
 * @property int $id
 * @property int $profile_officer_id
 * @property string $language
 * @property string $type
 * @property string|null $year
 * @property string|null $results_certificate
 */
class LanguageQualification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'language_qualification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profile_officer_id', 'language', 'type'], 'required'],
            [['profile_officer_id'], 'integer'],
            [['year'], 'safe'],
            [['language', 'type'], 'string', 'max' => 50],
            [['results_certificate'], 'string', 'max' => 100],
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
            'language' => Yii::t('app', 'Language'),
            'type' => Yii::t('app', 'Type'),
            'year' => Yii::t('app', 'Year'),
            'results_certificate' => Yii::t('app', 'Results Certificate'),
        ];
    }
}
