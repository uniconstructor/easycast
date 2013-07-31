<?php

$basePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'..';
$consoleConfig = CMap::mergeArray(
    require(dirname(__FILE__).'/production.php'),
    array(
        'basePath'=>$basePath,
        'language' => 'ru',
        'sourceLanguage' => 'en_us',
    
        // autoloading model and component classes
        'import'=>array(
            'application.models.*',
            'application.components.*',
            'application.modules.user.models.*',
            'application.modules.user.components.*',
            'application.extensions.*'
        ),
    
        // preloading 'log' component
        'preload'=>array('log', 'messages'),
    
        'modules'=>array(
            'user'=>array(),
            'questionary' => array(),
            'catalog' => array(),
            'admin' => array(),
            'projects' => array(),
            'calendar' => array(),
            'yii-forum' => array(),
            'rights'=>array(
                'install'=>false,
            ),
        ),
    
        // application components
        'components'=>array(
            
            // устанавливаем пареметр hostInfo для того чтобы из консоли могли создаваться абсолютные ссылки
            // ( http://www.yiiframework.com/forum/index.php/topic/14825-problem-with-createurl-and-createabsoluteurl-in-console-application/ )
            'request' => array(
                'hostInfo'  => 'http://easycast.ru',
                'baseUrl'   => '',
                'scriptUrl' => '',
            ),
            
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    array(
                        'class'=>'CDbLogRoute',
                        'connectionID' => 'db',
                        'levels'=>'error, warning, info, AWS',
                        'autoCreateLogTable' => true,
                    ),
                    array(
                        'class'=>'CEmailLogRoute',
                        'levels'=>'error, warning, AWS',
                        'emails'=>'php1602agregator@gmail.com',
                    ),
                ),
            ),
        ),
    )
);

unset($consoleConfig['controllerMap']);
unset($consoleConfig['theme']);

return $consoleConfig;