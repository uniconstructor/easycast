<?php
/**
 * @see http://www.yiiframework.com/doc-2.0/guide-tutorial-shared-hosting.html
 */

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'user' => [
            // following line will restrict access to admin page
            'as backend' => 'dektrium\user\filters\BackendFilter',
        ],
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'identityCookie' => [
                'name' => '_backendIdentity',
                'path' => '/backend',
                'httpOnly' => true,
            ],
            'enableAutoLogin' => true,
        ],
        'request' => [
            'csrfParam' => '_backendCSRF',
            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/backend',
            ],
        ],
        'session' => [
            'name' => 'BACKENDSESSID',
            'cookieParams' => [
                'path' => '/backend',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'params' => $params,
];