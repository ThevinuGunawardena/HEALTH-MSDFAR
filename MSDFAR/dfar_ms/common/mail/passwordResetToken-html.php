<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var string $resetLink */

use yii\helpers\Html;

?>

<p>Hello <?= Html::encode($user->username ?? $user->email) ?>,</p>

<p>
    We received a request to reset your password.
</p>

<p>
    <?= Html::a(
        'Reset Password',
        $resetLink,
        [
            'style' => '
                display: inline-block;
                padding: 10px 20px;
                background-color: #007bff;
                color: #ffffff;
                text-decoration: none;
                border-radius: 4px;
            ',
        ]
    ) ?>
</p>

<p>
    This password reset link will expire in five minutes.
</p>

<p>
    If you did not request a password reset, you can ignore this email.
</p>