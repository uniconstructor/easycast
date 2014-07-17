<?php
// EasyCast production index

// @todo пока не решен вопрос с автоматической настройкой серверов Amazon - устанавливаем все параметры здесь
ini_set('max_execution_time', '18000');
ini_set('max_input_time', '18000');
ini_set('date.timezone', 'Europe/Moscow');
ini_set('upload_max_filesize', '5000M');
ini_set('post_max_size', '5000M');


// change the following paths if necessary
$yii    = dirname(__FILE__).'/framework/yiilite.php';
$config = dirname(__FILE__).'/protected/config/production.php';

if ( isset($_SERVER['YII_DEBUG']) AND $_SERVER['YII_DEBUG'] == 1 )
{// добавляем возможность включать отладку YII настройками Elastic Beanstalk 
    define('YII_DEBUG', true);
}

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();