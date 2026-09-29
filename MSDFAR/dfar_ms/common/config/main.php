<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'timeZone' => 'Asia/Colombo',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mail.fisheriesdept.gov.lk',
                'username' => 'departure1@fisheriesdept.gov.lk',
                'password' => 'DFAR@123654',
                'port' => '587',
                'encryption' => 'tls',
                // 'streamOptions' => [
                //     'ssl' => [
                //         'allow_self_signed' => true,
                //         'verify_peer' => false,
                //         'verify_peer_name' => false,
                //     ],
                // ],
            ],
        ],
    ],
];
