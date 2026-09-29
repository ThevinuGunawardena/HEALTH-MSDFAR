<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $nic;
    public $password;
    public $rememberMe = true;

    private $_user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['nic', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
{
    if ($this->hasErrors()) {
        return;
    }

    $user = $this->getUser();

    if ($user === null) {
        $this->addError(
            $attribute,
            'Incorrect username or password.'
        );

        return;
    }

    /*
     * Account was already locked before this request.
     */
    if ($user->isLoginLocked()) {
        $remainingSeconds =
            $user->getRemainingLockoutSeconds();

        $remainingMinutes = max(
            1,
            (int) ceil($remainingSeconds / 60)
        );

        $this->addError(
            $attribute,
            'Account temporarily locked due to multiple failed login attempts. '
            . 'Please try again in '
            . $remainingMinutes
            . ' minute(s).'
        );

        return;
    }

    /*
     * Incorrect password.
     */
    if (!$user->validatePassword($this->password)) {
        $user->registerFailedLoginAttempt();

        /*
         * This failed attempt may have triggered
         * the progressive account lockout.
         */
        if ($user->isLoginLocked()) {
            $remainingSeconds =
                $user->getRemainingLockoutSeconds();

            $remainingMinutes = max(
                1,
                (int) ceil($remainingSeconds / 60)
            );

            $this->addError(
                $attribute,
                'Account temporarily locked due to multiple failed login attempts. '
                . 'Please try again in '
                . $remainingMinutes
                . ' minute(s).'
            );

            return;
        }

        $this->addError(
            $attribute,
            'Incorrect username or password.'
        );
    }
}

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
   public function login()
{
    if (!$this->validate()) {
        return false;
    }

    $user = $this->getUser();

    if ($user === null) {
        return false;
    }

    $loggedIn = Yii::$app->user->login(
        $user,
        0
    );

    /*
     * Successful authentication clears any previous
     * failed-login attempts and lockout information.
     */
    if ($loggedIn) {
        $user->resetFailedLoginAttempts();
    }

    return $loggedIn;
}

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->nic);
        }

        return $this->_user;
    }
}
