<?php

return [
    'id'   => 'easyCast',
    'name' => 'easyCast',
    'basePath' => dirname(dirname(dirname(__DIR__))),
    'aliases' => [
        '@protected' => '@app/protected',
        '@vendor'    => '@protected/vendor',
        '@runtime'   => '@protected/runtime',
        '@data'      => '@protected/data',
        '@cockpit'   => '@protected/vendor/aheinze/cockpit',
        '@assets'    => '@app/assets',
    ],
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=easycast',
            'username' => 'root',
            'password' => 'root',
        ],
        'cockpit' => [
            'class' => 'omnilight\cockpit\Cockpit',
            'cockpitConfigFile'  => '@runtime/cockpit/config.php',
            'cockpitStoragePath' => '@protected/data/cockpit',
            'config' => [
                'sec-key'  => 'c2109878c-009a-2787-ac66-ab558e8a15e5e1',
                'i18n'     => 'en',
                'app.name' => 'easyCast',
            ],
        ],
    ],
];