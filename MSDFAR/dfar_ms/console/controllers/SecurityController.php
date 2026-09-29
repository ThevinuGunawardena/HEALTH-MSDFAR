<?php

namespace console\controllers;

use common\models\User;
use yii\console\Controller;
use yii\console\ExitCode;

class SecurityController extends Controller
{
    public function actionInvalidateAllLogins(): int
    {
        $updatedCount = 0;
        $failedCount = 0;

        foreach (User::find()->each(500) as $user) {
            $user->generateAuthKey();

            if ($user->save(false, ['auth_key'])) {
                $updatedCount++;

                $this->stdout(
                    "Updated user ID: {$user->id}\n"
                );
            } else {
                $failedCount++;

                $this->stderr(
                    "Failed user ID: {$user->id}\n"
                );
            }
        }

        $this->stdout("Updated users: {$updatedCount}\n");
        $this->stdout("Failed users: {$failedCount}\n");

        return $failedCount === 0
            ? ExitCode::OK
            : ExitCode::UNSPECIFIED_ERROR;
    }
}