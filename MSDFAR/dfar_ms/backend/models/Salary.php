<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "salary".
 *
 * @property int $id
 * @property int $profile_officer_id
 * @property float $current_basic_salary
 * @property string|null $salary_increment_date
 */
class Salary extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'salary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['profile_officer_id', 'current_basic_salary'], 'required'],
            [['profile_officer_id'], 'integer'],
            [['current_basic_salary'], 'number'],
            [['salary_increment_date'], 'safe'],
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
            'current_basic_salary' => Yii::t('app', 'Current Basic Salary'),
            'salary_increment_date' => Yii::t('app', 'Salary Increment Date'),
        ];
    }
}
