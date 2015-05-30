<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases'    => [
        '@assets'    => '@app/web/assets',
        '@runtime'   => '@app/runtime',
        '@data'      => '@runtime/data',
        '@themes'    => '@app/themes',
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
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [],
                'yii\bootstrap\BootstrapPluginAsset' => [],
                //'yii\bootstrap\BootstrapThemeAsset' => [
                //    'sourcePath' => '@app/static',
                //    'css' => [
                //        YII_ENV_DEV ? 'css/bootstrap-theme.css' : 'css/bootstrap-theme.min.css',
                //    ],
                //],
            ],
        ],
    ],
];
