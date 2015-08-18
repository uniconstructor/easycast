<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        
    ],
    'components' => [
        'user' => [
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/views'   => '@app/themes/easycast',
                    //'@app/modules' => '@app/themes/easycast/modules',
                    //'@app/widgets' => '@app/themes/easycast/widgets',
                ],
                'baseUrl' => '@web/themes/easycast',
            ],
        ],
    ],
    'params' => $params,
];
