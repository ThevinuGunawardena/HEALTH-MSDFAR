<?php

namespace backend\services;

use backend\config\Constant;
use Yii;

class Util
{
    public static function editPermission(): bool
    {
        return Yii::$app->session->get("userPermission") == Constant::EDIT_PERMISSION || Yii::$app->session->get("userPermission") == Constant::ADMIN_PERMISSION;
    }

    public static function adminPermission(): bool
    {
        return Yii::$app->session->get("userPermission") == Constant::ADMIN_PERMISSION;
    }
}