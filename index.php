<?php
// EasyCast production index

/*$sapi = php_sapi_name();
if ( $sapi != 'cli' )
{// maintaince mode if needed
    //header('Location: http://easycast.ru/maintaince.html');
    //die('maintance');
}*/

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yiilite.php';
$config=dirname(__FILE__).'/protected/config/production.php';

// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();