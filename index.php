<?php
// EasyCast dev index

// change the following paths if necessary
$yii    = dirname(__FILE__).'/framework/yiilite.php';
$config = dirname(__FILE__).'/protected/config/dev.php';


define('YII_EASYCAST_CONFIG_DB_CONNECTION_STRING', 'mysql:host=localhost;dbname=easycast');
define('YII_EASYCAST_CONFIG_DB_LOGIN', 'root');
define('YII_EASYCAST_CONFIG_DB_PASSWORD', 'root');
// задаем временную зону вручную - новая версия PHP может некорректно работать с настройкой php.ini
date_default_timezone_set('Europe/Moscow');

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG', true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

require_once($yii);
Yii::createWebApplication($config)->run();