<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "fisherman".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $preferred_name_for_id
 * @property string $nic
 * @property string $passport
 * @property string $dob
 * @property string $gender
 * @property string $permanent_address
 * @property string $current_address
 * @property string $blood_group
 * @property string $mobile
 * @property string $fixed_line
 * @property string $email
 * @property int $status
 */
class Fisherman extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile_fisherman';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'preferred_name_for_id', 'nic', 'passport', 'dob', 'gender', 'permanent_address', 'current_address', 'blood_group', 'mobile',  'email', 'status'], 'safe'],
            [['dob','fixed_line'], 'safe'],
            [['status'], 'integer'],
            [['first_name', 'last_name', 'preferred_name_for_id', 'email'], 'string', 'max' => 200],
            [['nic', 'passport'], 'string', 'max' => 100],
            [['gender'], 'string', 'max' => 20],
            [['permanent_address', 'current_address'], 'string', 'max' => 500],
            [['blood_group', 'mobile', 'fixed_line'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'preferred_name_for_id' => Yii::t('app', 'Preferred Name For ID'),
            'nic' => Yii::t('app', 'NIC'),
            'passport' => Yii::t('app', 'Passport'),
            'dob' => Yii::t('app', 'Dob'),
            'gender' => Yii::t('app', 'Gender'),
            'permanent_address' => Yii::t('app', 'Permanent Address'),
            'current_address' => Yii::t('app', 'Current Address'),
            'blood_group' => Yii::t('app', 'Blood Group'),
            'mobile' => Yii::t('app', 'Mobile'),
            'fixed_line' => Yii::t('app', 'Fixed Line'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getSkipper()
{
    return $this->hasOne(Skipper::class, ['fisherman_id' => 'id']);
}
}
