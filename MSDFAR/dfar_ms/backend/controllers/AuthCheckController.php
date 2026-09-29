<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use backend\components\Controller;

use yii\web\Response;

class AuthCheckController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'files' => ['GET', 'HEAD'],
                ],
            ],
        ];
    }

    /**
     * Authentication endpoint used only by Nginx auth_request.
     *
     * Logged-in user: 204
     * Guest user:     403
     */
    public function actionFiles()
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;

        $response->headers->set(
            'Cache-Control',
            'no-store, no-cache, must-revalidate'
        );
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        if (Yii::$app->user->isGuest) {
            $response->statusCode = 403;

            if (Yii::$app->session->getIsActive()) {
                Yii::$app->session->close();
            }

            return '';
        }

        if (Yii::$app->session->getIsActive()) {
            Yii::$app->session->close();
        }

        $response->statusCode = 204;

        return '';
    }
}