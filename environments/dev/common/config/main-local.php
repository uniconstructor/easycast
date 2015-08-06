<?php

return [
    'components' => [
        'olddb'   => [
            'class'       => 'yii\db\Connection',
            'dsn'         => 'mysql:host=mysql.dev;dbname=easycast',
            'username'    => 'root',
            'password'    => 'root',
            'charset'     => 'utf8',
            'tablePrefix' => 'bgl_',
        ],
        'mongodb' => [
            'class' => '\yii\mongodb\Connection',
            'dsn'   => 'mongodb://mongodb.dev:27017/easycast',
        ],
        'mailer'  => [
            'class'            => 'yii\swiftmailer\Mailer',
            'viewPath'         => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
