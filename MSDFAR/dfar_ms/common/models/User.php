<?php

namespace common\models;

use backend\config\Constant;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $nic
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string|null $reset_token_expires_at 
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $type
 * @property integer $user_permission
 * @property int $failed_login_attempts
 * @property int|null $locked_until
 * @property int|null $last_failed_login_at
 * @property integer $profile_id
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

   const MAX_FAILED_LOGIN_ATTEMPTS = 5;

/*
 * Failed attempts observation window.
 */
const LOGIN_ATTEMPT_WINDOW = 900;

/*
 * Progressive lockout durations.
 */
const FIRST_LOCKOUT_DURATION = 300;   // 5 minutes
const SECOND_LOCKOUT_DURATION = 900;  // 15 minutes


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by nic
     *
     * @param string $nic
     * @return static|null
     */
    public static function findByUsername($nic)
    {
        return static::findOne(['nic' => $nic, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by profile id
     *
     * @param string $nic
     * @return static|null
     */
    public static function findByProfileId($id)
    {
        return static::findOne(['profile_id' => $id, 'type' => Constant::FISHERMAN]);
    }

    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
{
    return Yii::$app->security->validatePassword(
        $password,
        $this->password_hash
    );
}
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getOfficerProfile()
{
    return $this->hasOne(\backend\models\ProfileOfficers::class, ['id' => 'profile_id']);
}
/**
 * Returns true when the account is currently locked.
 */
public function isLoginLocked(): bool
{
    return $this->locked_until !== null
        && (int) $this->locked_until > time();
}


/**
 * Remaining lockout duration in seconds.
 */
public function getRemainingLockoutSeconds(): int
{
    if (!$this->isLoginLocked()) {
        return 0;
    }

    return max(
        0,
        (int) $this->locked_until - time()
    );
}


/**
 * Register a failed password attempt.
 */
public function registerFailedLoginAttempt(): void
{
    $now = time();

    /*
     * If a previous lock has expired, clear only
     * the failed-attempt counter.
     *
     * Keep lockout_count so the next lock can
     * use the increased duration.
     */
    if (
        $this->locked_until !== null
        && (int) $this->locked_until <= $now
    ) {
        $this->failed_login_attempts = 0;
        $this->locked_until = null;
        $this->last_failed_login_at = null;
    }

    /*
     * Start a new attempt window if the previous
     * failed attempt is too old.
     */
    if (
        $this->last_failed_login_at === null
        || (
            $now - (int) $this->last_failed_login_at
        ) > self::LOGIN_ATTEMPT_WINDOW
    ) {
        $this->failed_login_attempts = 1;
    } else {
        $this->failed_login_attempts =
            (int) $this->failed_login_attempts + 1;
    }

    $this->last_failed_login_at = $now;

    /*
     * Lock after 5 failed password attempts.
     */
    if (
        (int) $this->failed_login_attempts
        >= self::MAX_FAILED_LOGIN_ATTEMPTS
    ) {
        $this->lockout_count =
            (int) $this->lockout_count + 1;

        /*
         * First lock = 5 minutes.
         * Second and subsequent locks = 15 minutes.
         */
        $lockDuration =
            (int) $this->lockout_count === 1
                ? self::FIRST_LOCKOUT_DURATION
                : self::SECOND_LOCKOUT_DURATION;

        $this->locked_until =
            $now + $lockDuration;
    }

    $this->save(
        false,
        [
            'failed_login_attempts',
            'lockout_count',
            'locked_until',
            'last_failed_login_at',
        ]
    );
}

/**
 * Clear failed login data after successful login.
 */
public function resetFailedLoginAttempts(): void
{
    $this->failed_login_attempts = 0;
    $this->lockout_count = 0;
    $this->locked_until = null;
    $this->last_failed_login_at = null;

    $this->save(
        false,
        [
            'failed_login_attempts',
            'lockout_count',
            'locked_until',
            'last_failed_login_at',
        ]
    );
}
}
