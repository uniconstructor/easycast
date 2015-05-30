<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases'    => [
        '@assets'    => '@app/web/assets',
        '@runtime'   => '@app/runtime',
        '@data'      => '@runtime/data',
        '@cockpit'   => '@vendor/aheinze/cockpit',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
    ],
];
