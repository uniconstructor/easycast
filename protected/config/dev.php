<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'modules' => array(
            // uncomment the following to enable the Gii tool
            'gii' => array(
                'class'=>'system.gii.GiiModule',
                'password'=>'12345',
                // If removed, Gii defaults to localhost only. Edit carefully to taste.
                'ipFilters'=>array('127.0.0.1','::1'),
                'generatorPaths' => array(
                    'application.gii',  //nested set  Model and Crud templates
                    'bootstrap.gii',
                ),
            ),
            'log' => array(
                'routes'=> array(
                        array(
                         'class'=>'CEmailLogRoute',
                            'levels'=>'error, warning',
                            'emails'=>'php1602agregator@gmail.com',
                        ),
                        array(
                            'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                            'ipFilters'=>array('127.0.0.1','192.168.1.215'),
                        ),
                    )
                )
            ),
        'components' => array(
            'db' => array(
                // данные для работы сайта локально (для разработки)
                'connectionString' => 'mysql:host=localhost;dbname=easycast',
                'username' => 'root',
                'password' => 'root',
            )
        ),
        'params'=>array(
            // адрес с которого рассылаются все письма
            'adminEmail'=>'admin@easycast.ru',
            // API ID на vkontakte.ru (чтобы работал виджет "мне нравится")
            'vkontakteApiId' => '3534064',
            
            // Данные для доступа к кластеру Amazon (отключено, чтобы не посылать запросы с машины разработчика)
            'AWSAccessKey' => 'AKIAISQJ47JQQ2QOGBKA',
            'AWSSecret'    => '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',
            // использовать хостинг amazon s3 для хранения картинок
            'useAmazonS3'   => true,
            // использовать сервисы amazon SES для отправки почты
            'useAmazonSES'  => false,
            'AWSBucket'     => 'test.easycast.ru',
            'AWSBucketPath' => 'http://test.easycast.ru.s3.amazonaws.com',
        ),
    )
);