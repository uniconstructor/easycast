<?php
// EasyCast development version index

// параметры соединения с БД
define('YII_EASYCAST_CONFIG_DB_CONNECTION_STRING', 'mysql:host=localhost;dbname=easycast');
define('YII_EASYCAST_CONFIG_DB_LOGIN', 'root');
define('YII_EASYCAST_CONFIG_DB_PASSWORD', 'root');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
// specify enfiroment
defined('YII_ENV') or define('YII_ENV', 'dev');

// install Composer autoloader
require(__DIR__ . '/protected/vendor/autoload.php');

// путь к основному классу Yii объединяющий первую и вторую версии
require(__DIR__ . '/protected/components/Yii.php');

// задаем временную зону вручную - новая версия PHP может некорректно работать с настройкой php.ini
date_default_timezone_set('Europe/Moscow');

// путь к конфигурации Yii 2.x
$yii2Config = require(__DIR__ . '/protected/config/yii2/dev.php');
new yii\web\Application($yii2Config); // Do NOT call run()

/*set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext){
    $info = array(
        'errno' => $errno,
        'errstr' => $errstr,
        'errfile' => $errfile,
        'errline' => $errline,
        'errcontext' => $errcontext,
    );
    throw new Exception(print_r($info, true));
});*/

// путь к конфигурации Yii 1.x
$yii1Config = __DIR__ . '/protected/config/dev.php';
Yii::createWebApplication($yii1Config)->run();