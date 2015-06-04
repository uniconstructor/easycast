<?php
return [
    'components' => [
        'olddb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.$_SERVER['RDS_ENDPOINT'].';dbname=easycast',
            'username' => $_SERVER['RDS_LOGIN'],
            'password' => $_SERVER['RDS_PASS'],
            'charset' => 'utf8',
            'tablePrefix' => 'bgl_',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
