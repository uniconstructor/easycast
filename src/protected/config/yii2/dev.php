<?php

return [
    'id' => 'easyCast',
    'basePath' => dirname(dirname(__DIR__)),
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=easycast',
            'username' => 'root',
            'password' => 'root',
        ],
    ],
];