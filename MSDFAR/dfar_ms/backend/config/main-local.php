<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'NgPTZWPEObaVOXMp9CVFL8D5kak5qbet',
            'csrfCookie' => [
                'name' => '_csrf-backend',
                'path' => '/',
                'httpOnly' => true,
                'secure' => false,
            ],
        ],
        'user' => [
            'identityCookie' => [
                'name' => '_identity-backend',
                'path' => '/',
                'httpOnly' => true,
                'secure' => false,
            ],
        ],
        'session' => [
            'name' => 'advanced-backend',
            'cookieParams' => [
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'secure' => false,
            ],
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
