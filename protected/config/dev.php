<?php

return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),
    array(
        'modules' => array(
            // uncomment the following to enable the Gii tool
            'gii' => array(
                'class'    => 'system.gii.GiiModule',
                'password' => '12345',
                // If removed, Gii defaults to localhost only. Edit carefully to taste.
                'ipFilters'=>array('127.0.0.1','::1'),
                'generatorPaths' => array(
                    'application.gii',  //nested set  Model and Crud templates
                    'bootstrap.gii',
                ),
            ),
            'log' => array(
                'routes'=> array(
                        /*array(
                         'class'=>'CEmailLogRoute',
                            'levels'=>'error, warning',
                            'emails'=>'php1602agregator@gmail.com',
                        ),*/
                        array(
                            'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                            'ipFilters'=>array('127.0.0.1','192.168.1.215'),
                        ),
                    )
                ),
            // бонус: мой маленький робот для торговли на бирже :)
            // Он вообще к сайту никак не относится, просто воткнуть его сюда было быстрее чем
            // создавать новый проект :)
            /*'trader' => array(
                'class' => 'application.modules.trader.TraderModule',
            ),*/
            
            'questionary' => array(
                'controllerMap' => array(
                    // задаем путь к контроллеру загрузки изображений (для анкеты)
                    'gallery' => array(
                        'handlerClass'    => 'GmUploadedPhoto',
                        'customBehaviors' => array(),
                    ),
                ),
            ),
        ),

        'components' => array(
            // настройки соединения с БД (для среды разработчика)
            'db' => array(
                // данные для работы сайта локально (для разработки)
                'connectionString' => 'mysql:host=localhost;dbname=easycast',
                'username' => 'root',
                'password' => 'root',
            ),
            // API для работы с Мегапланом: в среде разработчика создаем задачи только для себя,
            // не указывая никого в качестве соисполнителей или аудиторов, чтобы не отвлекать от работы остальных
            'megaplan' => array(
                'debug'             => true,
                'accessId'          => '3191561bcf6adc2Ab125',
                'secretKey'         => '3F62c64cf3fd6daC1c2543a45720Ed666e4d920f',
                'defaultUserId'     => '1000027',
                'defaultEmployeeId' => '1000000',
                'auditors'          => array('1000004'),
                'projectManagers'   => array('1000004'),
            ),
        ),
        
        // другие параметры приложения
        'params' => array(
            // API ID на vkontakte.ru (чтобы работал виджет "мне нравится")
            'vkontakteApiId' => '3534064',
            
            // Настройки хостинга Amazon
            // Данные для доступа к кластеру Amazon (отключено, чтобы не посылать запросы с машины разработчика)
            'AWSAccessKey' => 'AKIAISQJ47JQQ2QOGBKA',
            'AWSSecret'    => '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',
            //'AWSAccessKey' => '',
            //'AWSSecret'    => '',
            
            // S3
            // Использовать хостинг amazon s3 для хранения картинок
            'useAmazonS3'   => false,
            'AWSBucket'     => 'test.easycast.ru',
            'AWSBucketPath' => 'http://bglance',
            //'AWSBucketPath' => 'http://test.easycast.ru.s3.amazonaws.com',
            // SES
            // использовать сервисы amazon SES для отправки почты
            'useAmazonSES'  => false,
            // SQS
            // использовать Amazon SES для отправки большого количества почты (через очередь)
            'useAmazonSQS'      => true,
            'AWSEmailQueueUrl'  => 'https://sqs.us-east-1.amazonaws.com/507109426938/test_easycast_mail',
            'AWSEmailQueueName' => 'test_easycast_mail',
            
            // Для отладки: отсылать ли вообще хоть какие-нибудь сообщения, даже на тестовые адреса?
            'AWSSendMessages' => true,
            
            // использовать ли прокси сервера google для отображения картинок в письмах
            // (должно быть включено на production и выключено на машине разработчика)
            'useGoogleImageProxy' => false,
        ),
    )
);