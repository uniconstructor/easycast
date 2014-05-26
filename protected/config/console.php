<?php

$basePath = dirname(__FILE__).DIRECTORY_SEPARATOR.'..';
$consoleConfig = CMap::mergeArray(
    require(dirname(__FILE__).'/dev.php'),
    array(
        'basePath' => $basePath,
    
        // autoloading model and component classes
        'import' => array(
            'application.models.*',
            'application.components.*',
            'application.modules.user.models.*',
            'application.modules.user.components.*',
            'application.extensions.*'
        ),
    
        // preloading components
        'preload' => array('log', 'messages'),
    
        // application components
        'components' => array(
            // устанавливаем пареметр hostInfo для того чтобы из консоли могли создаваться абсолютные ссылки
            // ( http://www.yiiframework.com/forum/index.php/topic/14825-problem-with-createurl-and-createabsoluteurl-in-console-application/ )
            'request' => array(
                'hostInfo'  => 'http://bglance',
                'baseUrl'   => '',
                'scriptUrl' => '',
            ),
            
            'log' => array(
                'class'  => 'CLogRouter',
                'routes' => array(
                    array(
                        'class'        => 'CDbLogRoute',
                        'connectionID' => 'db',
                        'levels'       => 'error, warning, info, AWS',
                        'autoCreateLogTable' => true,
                    ),
                ),
            ),
        ),
    )
);

unset($consoleConfig['controllerMap']);
unset($consoleConfig['theme']);

return $consoleConfig;