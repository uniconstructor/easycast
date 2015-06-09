<?php

$extensions = require(dirname(dirname(__DIR__)) . '/vendor/yiisoft/extensions.php');

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases'    => [
        '@assets'    => '@app/web/assets',
        '@runtime'   => '@app/runtime',
        '@data'      => '@runtime/data',
        '@themes'    => '@app/themes',
        '@cockpit'   => '@vendor/aheinze/cockpit',
    ],
    'extensions' => $extensions,
    'components' => [
        'cache' => [
            'class' => 'yii\caching\DummyCache',
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
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'jsOptions' => [
                        'position' => yii\web\View::POS_HEAD,
                    ],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'jsOptions' => [
                        'position' => yii\web\View::POS_HEAD,
                    ],
                ],
                'yii\web\JqueryAsset' => [
                    'jsOptions' => [
                        'position' => yii\web\View::POS_HEAD,
                    ],
                ],
            ],
        ],
    ],
    'controllerMap' => [
        'page' => 'common\components\PageController',
    ],
];
