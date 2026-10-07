<?php

namespace backend\components;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class Controller extends \yii\web\Controller
{
    private const PUBLIC_ROUTES = [
        'site/index',
        'site/about',
        'site/contact',
        'site/login',
        'site/signup',
        'site/verify-email',
        'site/resend-verification-email',
        'site/error',
        'site/license-validation',
        'site/captcha',
        'site/request-password-reset',
        'site/reset-password',
        'site/expire',
        'site/sso-to-health',
        'error/index',
        'api/v1/report-website',
        'api/v1/catch-production',
        'api/v1/get-boat-departure-by-id',  
        'api/v1/update-vms-payment',
        'departure/create',
        'reports/website',
        'departure/create',
        'inquiry/create',
        'departure/submitted',
        'reports/departure-status',
        'reports/departure-skipper-status',
        'api/v1/production-data',
        'api/v1/boat-data-by-harbour',
        


        // Boat number AJAX lookup
        'departure/boatdetails',
        'departure/getskipper',
        'departure/getdepartureboatdetails'


    ];

    public function beforeAction($action)
    {
        try {
            if (!parent::beforeAction($action)) {
                return false;
            }
        } catch (BadRequestHttpException $exception) {
            if (Yii::$app->request->isAjax) {
                return $this->sendErrorResponse(
                    400,
                    'Invalid Token',
                    'Invalid or expired security token.'
                );
            }

            throw $exception;
        }

        $route = $action->uniqueId;

        if (
            !in_array($route, self::PUBLIC_ROUTES, true) &&
            Yii::$app->user->isGuest
        ) {
            if (Yii::$app->request->isAjax) {
                return $this->sendErrorResponse(
                    401,
                    'Unauthorized',
                    'Invalid or expired session. Please log in again.'
                );
            }

            throw new UnauthorizedHttpException(
                'Invalid or expired session. Please log in again.'
            );
        }

        return true;
    }

    private function sendErrorResponse(
        int $statusCode,
        string $error,
        string $message
    ): bool {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->statusCode = $statusCode;
        Yii::$app->response->data = [
            'status' => $statusCode,
            'error' => $error,
            'message' => $message,
        ];

        // Stops the controller action from executing.
        return false;
    }
}