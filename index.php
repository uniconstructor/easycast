<?php
// EasyCast production index

// @todo пока не решен вопрос с автоматической настройкой серверов Amazon - устанавливаем все параметры здесь
ini_set('max_execution_time', '9001');
ini_set('date.timezone', 'Europe/Moscow');
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '11M');


// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yiilite.php';
$config=dirname(__FILE__).'/protected/config/production.php';

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();