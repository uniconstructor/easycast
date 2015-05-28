<?php

return [
    'id'         => 'easyCast',
    'name'       => 'easyCast',
    'basePath'   => dirname(dirname(dirname(__DIR__))),
    'aliases'    => [
        '@assets'    => '@app/assets',
        '@themes'    => '@app/themes',
        '@protected' => '@app/protected',
        '@config'    => '@protected/config',
        '@vendor'    => '@protected/vendor',
        '@runtime'   => '@protected/runtime',
        '@data'      => '@protected/data',
        '@cockpit'   => '@protected/vendor/aheinze/cockpit',
    ],
    'components' => [
        'db' => [
            'class'    => 'yii\db\Connection',
            'dsn'      => 'mysql:host=localhost;dbname=easycast',
            'username' => 'root',
            'password' => 'root',
        ],
        // Cockpit CMS: ядро админки
        // @see http://getcockpit.com
        'cockpit' => [
            'class'              => 'omnilight\cockpit\Cockpit',
            'cockpitConfigFile'  => '@runtime/cockpit/config.php',
            'cockpitStoragePath' => '@protected/data/cockpit',
            'baseRoute'          => '/admin/admin/cockpit',
            'baseUrl'            => '/admin/admin/cockpit',
            'config'             => [
                'debug'    => true,
                'sec-key'  => 'c2109878c-009a-2787-ac66-ab558e8a15e5e1',
                'i18n'     => 'ru',
                'app.name' => 'easyCast',
            ],
        ],
    ],
];
