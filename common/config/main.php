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
    'modules' => [
        // GUI для управления пользователями
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => true,
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\mongodb\Cache',
        ],
        'session' => [
            'class' => 'yii\mongodb\Session',
        ],
        'formatter' => [
            'dateFormat'        => 'dd.MM.yyyy',
            'datetimeFormat'    => 'HH:mm:ss dd.MM.yyyy',
            'decimalSeparator'  => ',',
            'thousandSeparator' => ' ',
            'currencyCode'      => 'RUR',
            'locale'            => 'ru-RU',
        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => false,
            'rules' => [
                //'module/<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'assetManager' => [
            // создаем символические ссылки на assets вместо копирования
            'linkAssets' => true,
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
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
    ],
    'controllerMap' => [
        'page'            => 'common\components\PageController',
        'mongodb-migrate' => 'yii\mongodb\console\controllers\MigrateController',
    ],
];
