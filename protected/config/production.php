<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'components' => array(
            'db' => array(
                // данные для работы сайта в сети (amazon RDS)
                'connectionString' => 'mysql:host=aa1ag10r3jn7rqy.c3u48hx0c3om.us-east-1.rds.amazonaws.com;dbname=easycast',
                'username' => 'root',
			    'password' => 'M2XJcWWGdHS6MSgD',
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CDbLogRoute',
                        'connectionID' => 'db',
                        'levels'=>'error, warning, info, application',
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