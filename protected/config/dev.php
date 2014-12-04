<?php

/**
 * Конфигурация версии разработчика
 */
return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'modules' => array(
            // для версии разработчика включаем Gii-генерацию кода
            'gii' => array(
                'class'     => 'system.gii.GiiModule',
                'password'  => '12345',
                'ipFilters' => array('127.0.0.1', '::1'),
                'generatorPaths' => array(
                    'application.gii',
                    'bootstrap.gii',
                    'ext.simpleWorkflow.command.gii',
                ),
            ),
        ),
        'components' => array(
            // настройки соединения с БД (для среды разработчика)
            'db' => array(
                // данные для работы сайта локально (для разработки)
                'connectionString'   => 'mysql:host=localhost;dbname=easycast',
                'username'           => 'root',
                'password'           => 'root',
                'enableProfiling'    => false,
                'enableParamLogging' => false,
            ),
            // сессия пользователя
            'session' => array(
                'autoCreateSessionTable' => false,
            ),
            // трассировка и логи
            'log' => array(
                'class'  => 'CLogRouter',
                'routes' => array(
                    // виджет разработчика для отладки приложения
                    // (папка с виджетом должна обязательно называться yii-debug-toolbar)
                    /*'YiiDebugToolbarRoute' => array(
                        'class' => 'application.extensions.yii-debug-toolbar.YiiDebugToolbarRoute',
                        'panels' => array(
                            'YiiDebugToolbarPanelRequest',
                            'YiiDebugToolbarPanelSettings',
                            'YiiDebugToolbarPanelViews',
                            'YiiDebugToolbarPanelSql',
                            'YiiDebugToolbarPanelLogging',
                        ),
                    ),*/
                    'CDbLogRoute' => array(
                        'class'              => 'CDbLogRoute',
                        'connectionID'       => 'db',
                        'autoCreateLogTable' => false,
                    ),
                ),
            ),
        ),
        // другие параметры приложения
        'params' => array(
            // API ID на vkontakte.ru (чтобы работал виджет "мне нравится")
            'vkontakteApiId' => '3534064',
            // Настройки хостинга Amazon
            // Данные для доступа к кластеру Amazon (можно отключить, чтобы не посылать запросы с машины разработчика)
            'AWSAccessKey' => 'AKIAISQJ47JQQ2QOGBKA',
            'AWSSecret'    => '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',
            // S3
            // Использовать хостинг amazon s3 для хранения картинок
            'useAmazonS3'    => true,
            'AWSBucket'      => 'test.easycast.ru',
            //'AWSBucketPath' => 'http://bglance',
            'AWSBucketPath'  => 'https://img.easycast.ru.s3.amazonaws.com',
            //'AWSBucketPath'  => 'http://test.easycast.ru.s3.amazonaws.com',
            'AWSVideoBucket' => 'video.easycast.ru',
            // SES
            // использовать сервис Amazon SES для отправки почты на реальные адреса
            // должно быть выключено в этом файле (dev.php)
            // включается только на production
            // в положении false все письма уходят только на один тестовый адрес
            'useAmazonSES'  => false,
            // SQS
            // использовать Amazon SQS для отправки большого количества почты (через очередь)
            'useAmazonSQS'      => false,
            'AWSEmailQueueUrl'  => 'https://sqs.us-east-1.amazonaws.com/507109426938/test_easycast_mail',
            'AWSEmailQueueName' => 'test_easycast_mail',
            // Для отладки: отсылать ли вообще хоть какие-нибудь сообщения, даже на тестовые адреса?
            'AWSSendMessages' => true,
            // использовать ли прокси сервера google для отображения картинок в письмах
            // (должно быть включено на production и выключено на машине разработчика)
            'useGoogleImageProxy' => false,
            // cron: на dev-системе сильно замедляет работу, поэтому отключен
            // включается только для целей тестирования
            'useCron' => false,
        ),
    )
);