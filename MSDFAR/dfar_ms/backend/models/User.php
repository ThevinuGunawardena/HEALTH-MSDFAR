<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $nic
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string|null $reset_token_expires_at
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 * @property int $profile_id
 * @property int $type
 * @property int $user_permission
 *
 * @property int $failed_login_attempts
 * @property int|null $locked_until
 * @property int|null $last_failed_login_at
* @property int|null $failed_login_attempts

 * 
 * @property ApprovalLog[] $approvalLogs
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nic', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at', 'profile_id', 'user_permission', 'failed_login_attempts', 'locked_until', 'last_failed_login_at', 'failed_login_attempts'], 'integer'],            [['nic', 'password_hash', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['type'], 'string'],
            [['nic'], 'unique'],
            [['password_reset_token', 'reset_token_expires_at', 'verification_token'], 'default', 'value' => null], 
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nic' => Yii::t('app', 'Nic'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'verification_token' => Yii::t('app', 'Verification Token'),
            'profile_id' => Yii::t('app', 'Profile ID'),
            'type' => Yii::t('app', 'Type'),
            'user_permission' => Yii::t('app', 'User Permission'),
        ];
    }

    /**
     * Gets query for [[ApprovalLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovalLogs()
    {
        return $this->hasMany(ApprovalLog::class, ['done_by' => 'id']);
    }

    public function getOfficerProfile()
{
    return $this->hasOne(ProfileOfficer::class, ['id' => 'profile_id']);
}
}
