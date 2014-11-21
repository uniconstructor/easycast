<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'components' => array(
            // данные для подключения к серверу БД (используется amazon RDS)
            // серверные переменные заданы в параметрах Elastic Beanstalk
            // чтобы увидеть или изменить их - нужно зайти в контрольную панель Амазоновского хостинга
            // @see https://console.aws.amazon.com/elasticbeanstalk/home?region=us-east-1
            // выбрать среду (enviroment) в которой работает сайт и изменить ее настройки
            'db' => array(
                'connectionString' => 'mysql:host='.$_SERVER['RDS_ENDPOINT'].';dbname=easycast',
                'username'         => $_SERVER['RDS_LOGIN'],
			    'password'         => $_SERVER['RDS_PASS'],
            ),
            // настройки CWebUser
            'user' => array(
                // делаем так чтобы авторизация по cookie работала на easycast.ru и www.easycast.ru
                'identityCookie' => array('domain' => '.easycast.ru'),
            ),
            // работа с сессией
            'session' => array(
                // делаем так чтобы авторизация по cookie работала на easycast.ru и www.easycast.ru
                'cookieParams'           => array('domain' => '.easycast.ru'),
                'autoCreateSessionTable' => false,
            ),
            // логи и трассировка
            // @todo хранить логи production-сервера отдельно в NoSQL хранилище
            'log' => array(
                'class'  => 'CLogRouter',
                'routes' => array(
                    'CDbLogRoute' => array(
                        'class'              => 'CDbLogRoute',
                        'connectionID'       => 'db',
                        'levels'             => 'error, warning',
                        'except'             => 'exception.CHttpException.404, simpleWorkflow',
                        'autoCreateLogTable' => false,
                    ),
                ),
            ),
        ),
        // другие параметры приложения
        'params' => array(
            // API ID на vkontakte.ru (чтобы работал виджет "мне нравится")
            'vkontakteApiId' => '3534064',
            // Данные для доступа к кластеру Amazon
            // @see https://console.aws.amazon.com/elasticbeanstalk/home?region=us-east-1
            'AWSAccessKey' => $_SERVER['AWS_ACCESS_KEY_ID'],
            'AWSSecret'    => $_SERVER['AWS_SECRET_KEY'],
            // S3
            // Использовать хостинг amazon s3 для хранения картинок
            'useAmazonS3'    => true,
            'AWSBucket'      => 'img.easycast.ru',
            'AWSBucketPath'  => 'http://img.easycast.ru.s3.amazonaws.com',
            'AWSVideoBucket' => 'video.easycast.ru',
            // SES
            // использовать сервисы amazon SES для отправки почты
            'useAmazonSES'   => true,
            // SQS
            // использовать Amazon SQS для отправки большого количества почты (через очередь)
            'useAmazonSQS'      => true,
            'AWSEmailQueueUrl'  => 'https://sqs.us-east-1.amazonaws.com/507109426938/easycast_mail',
            'AWSEmailQueueName' => 'easycast_mail',
            // Для отладки: отсылать ли вообще хоть какие-нибудь сообщения, даже на тестовые адреса?
            'AWSSendMessages'   => true,
            // использовать ли прокси сервера google для отображения картинок в письмах
            // (должно быть включено на production и выключено на машине разработчика)
            'useGoogleImageProxy' => true,
            // cron: на production-системе обязательно включен
            'useCron' => true,
        ),
    )
);