<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "promotion".
 *
 * @property int $id
 * @property int $profile_officer_id
 * @property string $promoted_as
 * @property string|null $promotion_letter
 * @property string|null $promotion_date
 */
class Promotion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profile_officer_id', 'promoted_as'], 'required'],
            [['profile_officer_id'], 'integer'],
            [['promotion_date'], 'safe'],
            [['promoted_as', 'promotion_letter'], 'string', 'max' => 100],
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
            'promoted_as' => Yii::t('app', 'Promoted As'),
            'promotion_letter' => Yii::t('app', 'Promotion Letter'),
            'promotion_date' => Yii::t('app', 'Promotion Date'),
        ];
    }
}
