<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'modules' => array(
            // для версии разработчика включаем Gii-генерацию кода
            'gii' => array(
                'class'     => 'system.gii.GiiModule',
                'password'  => '12345',
                'ipFilters' => array('127.0.0.1','::1'),
                'generatorPaths' => array(
                    'application.gii',
                    'bootstrap.gii',
                    'ext.simpleWorkflow.command.gii',
                ),
            ),
            'log' => array(
                'routes' => array(
                    array(
                        'class'     => 'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                        'ipFilters' => array('127.0.0.1'),
                    ),
                    array(
                        'class'              => 'CDbLogRoute',
                        // @todo хранить логи отдельно ото всех остальных данных для лучшей безопасности
                        'connectionID'       => 'db',
                        //'levels'             => 'error, warning, info, trace',
                        // не храним логи о 404 страницах - и логи simpleWorkflow
                        'except'             => 'CHttpException.404, simpleWorkflow',
                        'autoCreateLogTable' => false,
                    ),
                ),
            ),
            // бонус: мой маленький робот для торговли на бирже :)
            // Он вообще к сайту никак не относится, просто воткнуть его сюда было быстрее чем
            // создавать новый проект :)
            'trader' => array(
                'class' => 'application.modules.trader.TraderModule',
            ),
        ),

        'components' => array(
            // настройки соединения с БД (для среды разработчика)
            'db' => array(
                // данные для работы сайта локально (для разработки)
                'connectionString' => 'mysql:host=localhost;dbname=easycast',
                'username'         => 'root',
                'password'         => 'root',
            ),
            // API для работы с Мегапланом: в среде разработчика создаем задачи только для себя,
            // не указывая никого в качестве соисполнителей или аудиторов, чтобы не отвлекать от работы остальных
            'megaplan' => array(
                'debug'             => true,
                'accessId'          => '3191561bcf6adc2Ab125',
                'secretKey'         => '3F62c64cf3fd6daC1c2543a45720Ed666e4d920f',
                'defaultUserId'     => '1000027',
                'defaultEmployeeId' => '1000004',
                'auditors'          => array('1000004'),
                'projectManagers'   => array('1000004'),
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
            //'AWSAccessKey' => '',
            //'AWSSecret'    => '',
            
            // S3
            // Использовать хостинг amazon s3 для хранения картинок
            'useAmazonS3'    => true,
            'AWSBucket'      => 'test.easycast.ru',
            //'AWSBucketPath' => 'http://bglance',
            'AWSBucketPath'  => 'http://test.easycast.ru.s3.amazonaws.com',
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