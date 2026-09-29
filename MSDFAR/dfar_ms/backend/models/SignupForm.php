<?php

namespace backend\models;

use backend\config\Constant;
use common\models\User;
use Yii;
use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $nic;
    public $email;
    public $password;
    public $type;
    public $user_permission;
    public $user_role;
    public $secondary;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['nic', 'trim'],
            ['nic', 'required'],
            ['user_permission', 'required'],
            ['user_role', 'required'],
            ['user_permission', 'default', 'value' => Constant::EDIT_PERMISSION],
            ['user_role', 'default', 'value' => "FISHERMAN"],
            ['nic', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This NIC has already been taken.'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'This Email has already been taken.'],
            ['nic', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['type', 'required'],
            ['secondary', 'safe'],

            ['password', 'required'],
            ['password', 'string', 'min' => 8],
            [
                'password',
                'match',
                'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/',
                'message' => 'Password must contain at least 8 characters, including uppercase, lowercase, number, and special character.'
            ],
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signupFisherman()
    {
        $this->type = Constant::FISHERMAN;
        $this->user_role = Constant::FISHERMAN;
        $this->user_permission = Constant::EDIT_PERMISSION;

        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->nic = $this->nic;
        $user->email = $this->nic . "@hynetz.com";
        $user->type = $this->type;
        $user->profile_id = 0;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        if ($user->save()) {
            $auth = new AuthAssignment();
            $auth->user_id = $user->id;
            $auth->item_name = "FISHERMAN";
            return $auth->save();
        }
    }


    public function signupOfficers()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        if ($this->secondary) {
            $user->type = $this->type . ',' . implode(',', $this->secondary);
//            $user->type = $this->type . ',' . $this->secondary;
        } else {
            $user->type = $this->type;
        }
        $user->nic = $this->nic;
        $user->email = $this->email;

        $user->user_permission = $this->user_permission;
        $user->profile_id = 0;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        if ($user->save()) {
            return $user;
        }
    }

    public function resetPw($userId)
    {
//        print_r($this);exit();
//        if (!$this->validate()) {
//            return null;
//        }

        $user = User::findOne($userId);
        $user->setPassword($this->password);
        $user->generateAuthKey();

        if ($user->save()) {
            return $user;
        }
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->nic = $this->nic;
        $user->email = $this->email;
        $user->type = $this->type;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        return $user->save();
    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
