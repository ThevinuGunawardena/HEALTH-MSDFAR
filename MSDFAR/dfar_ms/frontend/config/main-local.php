<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'WYNlEFK1QqeGwaCwYEA-Jb4aX19lO9Qe',
        ],
    ],
];

if (YII_ENV_DEV && YII_DEBUG) {
    if (class_exists(\yii\gii\Module::class)) {
        $config['bootstrap'][] = 'gii';
        $config['modules']['gii'] = [
            'class' => \yii\gii\Module::class,
            'allowedIPs' => ['127.0.0.1', '::1', '*'],
        ];
    }
}
return $config;
