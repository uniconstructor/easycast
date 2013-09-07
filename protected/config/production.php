<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'components' => array(
            // данные для подключения к серверу БД (используется amazon RDS)
            // серверные переменные заданы в параметрах Elastic Beanstalk
            'db' => array(
                'connectionString' => 'mysql:host='.$_SERVER['RDS_ENDPOINT'].';dbname=easycast',
                'username' => $_SERVER['RDS_LOGIN'],
			    'password' => $_SERVER['RDS_PASS'],
            ),
            
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CDbLogRoute',
                        // @todo хранить логи отдельно ото всех остальных данных для лучшей безопасности
                        'connectionID' => 'db',
                        'levels' => 'error, warning, info, application',
                        'autoCreateLogTable' => false,
                    ),
                ),
            ),
        ),
        
        'params'=>array(
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
        ),
    )
);