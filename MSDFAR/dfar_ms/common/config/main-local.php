<?php

$dbPassword = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (function() {
    try {
        new PDO('mysql:host=localhost;dbname=dfar_ms', 'root', '12345');
        return '12345';
    } catch (\Throwable $e) {
        return '';
    }
})();

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=dfar_ms',
            'username' => 'root',
            'password' => $dbPassword,
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
