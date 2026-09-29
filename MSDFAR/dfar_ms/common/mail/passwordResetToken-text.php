<?php

/** @var common\models\User $user */
/** @var string $resetLink */

?>

Hello <?= $user->username ?? $user->email ?>,

We received a request to reset your password.

Use the following link to reset your password:

<?= $resetLink ?>


This password reset link will expire in five minutes.

If you did not request a password reset, you can ignore this email.