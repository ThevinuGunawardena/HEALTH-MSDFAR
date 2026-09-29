<?php

namespace backend\models;

use common\models\User;
use Yii;
use yii\base\Model;

class AuthenticatedPasswordChangeForm extends Model
{
    public $currentPassword;
    public $newPassword;
    public $confirmPassword;

    private ?User $user = null;

    public function rules(): array
    {
        return [
            [
                ['currentPassword', 'newPassword', 'confirmPassword'],
                'required',
            ],
            [
                ['currentPassword', 'newPassword', 'confirmPassword'],
                'string',
            ],
            [
                'newPassword',
                'string',
                'min' => 8,
                'max' => 72,
            ],
            [
                'confirmPassword',
                'compare',
                'compareAttribute' => 'newPassword',
                'message' => 'The new passwords do not match.',
            ],
            [
                'currentPassword',
                'validateCurrentPassword',
            ],
            [
                'newPassword',
                'validateNewPassword',
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'currentPassword' => 'Current Password',
            'newPassword' => 'New Password',
            'confirmPassword' => 'Confirm New Password',
        ];
    }

    public function validateCurrentPassword($attribute): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        $user = $this->getUser();

        if (
            $user === null ||
            !$user->validatePassword($this->$attribute)
        ) {
            $this->addError(
                $attribute,
                'The current password is incorrect.'
            );
        }
    }

    public function validateNewPassword($attribute): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        $user = $this->getUser();

        if (
            $user !== null &&
            $user->validatePassword($this->$attribute)
        ) {
            $this->addError(
                $attribute,
                'The new password must be different from the current password.'
            );
        }
    }

    public function changePassword(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        if ($user === null) {
            $this->addError(
                'currentPassword',
                'The authenticated user could not be found.'
            );

            return false;
        }

        $user->setPassword($this->newPassword);
        $user->generateAuthKey();

        return $user->save(
            false,
            ['password_hash', 'auth_key']
        );
    }

    private function getUser(): ?User
    {
        if ($this->user === null && !Yii::$app->user->isGuest) {
            $this->user = User::findOne(Yii::$app->user->id);
        }

        return $this->user;
    }
}