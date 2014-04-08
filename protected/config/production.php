<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'components' => array(
            // данные для подключения к серверу БД (используется amazon RDS)
            // серверные переменные заданы в параметрах Elastic Beanstalk
            // чтобы увидеть или изменить их - нужно зайти в контрольную панель Амазоновского хостинга
            // https://console.aws.amazon.com/elasticbeanstalk/home?region=us-east-1
            // выбрать среду (enviroment) в которой работает сайт и изменить ее настройки
            'db' => array(
                'connectionString' => 'mysql:host='.$_SERVER['RDS_ENDPOINT'].';dbname=easycast',
                'username'         => $_SERVER['RDS_LOGIN'],
			    'password'         => $_SERVER['RDS_PASS'],
            ),
            
            'user' => array(
                // делаем так чтобы авторизация по cookie работала на easycast.ru и www.easycast.ru
                'identityCookie' => array('domain' => '.easycast.ru'),
            ),
            
            'session' => array(
                // делаем так чтобы авторизация по cookie работала на easycast.ru и www.easycast.ru
                'cookieParams' => array('domain' => '.easycast.ru'),
            ),
            
            // API для работы с Мегапланом
            'megaplan' => array(
                'debug'             => false,
                'accessId'          => '3191561bcf6adc2Ab125',
                'secretKey'         => '3F62c64cf3fd6daC1c2543a45720Ed666e4d920f',
                'defaultUserId'     => '1000027',
                'defaultEmployeeId' => '1000000',
                'auditors'          => array('1000015', '1000005', '1000012'),
                // список id руководителей проектов в Мегаплане
                'projectManagers'   => array(
                    '1000001', // Ира
                    '1000005', // Алена 
                    '1000002', // Аня (Дуленкова)
                    '1000008', // Максим
                    '1000007', // Лиза
                    '1000010', // Вероника
                    '1000011', // Лёха (Михайлов)
                    '1000006', // Маруся
                    '1000012', // Петя
                    '1000004', // Я
                    '1000015', // Коля
                ),
            ),
            
            'log' => array(
                'class'  => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CDbLogRoute',
                        // @todo хранить логи отдельно ото всех остальных данных для лучшей безопасности
                        // @todo о, сюрприз! их еще и дорого хранить на RDS, ну отлично просто
                        'connectionID'       => 'db',
                        'levels'             => 'error, warning, info, application, AWS',
                        'autoCreateLogTable' => false,
                    ),
                ),
            ),
        ),
        
        'params' => array(
            // API ID на vkontakte.ru (чтобы работал виджет "мне нравится")
            'vkontakteApiId' => '3534064',
            
            // Данные для доступа к кластеру Amazon
            'AWSAccessKey' => 'AKIAISQJ47JQQ2QOGBKA',
            'AWSSecret'    => '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',
            
            // S3
            // Использовать хостинг amazon s3 для хранения картинок
            'useAmazonS3'   => true,
            'AWSBucket'     => 'img.easycast.ru',
            'AWSBucketPath' => 'http://img.easycast.ru.s3.amazonaws.com',
            // SES
            // использовать сервисы amazon SES для отправки почты
            'useAmazonSES'  => true,
            // SQS
            // использовать Amazon SQS для отправки большого количества почты (через очередь)
            'useAmazonSQS'      => true,
            'AWSEmailQueueUrl'  => 'https://sqs.us-east-1.amazonaws.com/507109426938/easycast_mail',
            'AWSEmailQueueName' => 'easycast_mail',
            
            // Для отладки: отсылать ли вообще хоть какие-нибудь сообщения, даже на тестовые адреса?
            'AWSSendMessages' => true,
            
            // использовать ли прокси сервера google для отображения картинок в письмах
            // (должно быть включено на production и выключено на машине разработчика)
            'useGoogleImageProxy' => true,
        ),
    )
);