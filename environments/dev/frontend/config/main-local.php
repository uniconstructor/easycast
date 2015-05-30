<?php

$config = [
    'components' => [
        'request' => [
            'cookieValidationKey' => 'Ow8tREAVzZ-jepKROnuUvSaxlOleSFt4',
        ],
    ],
];

if ( ! YII_ENV_TEST ) {
    // configuration adjustments for 'dev' environment
    //$config['bootstrap'][] = 'debug';
    //$config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
