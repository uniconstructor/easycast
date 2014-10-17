<?php
/**
 * @see http://www.yiiframework.ru/doc/guide/ru/test.overview
 */

define('TEST_BASE_URL', 'http://bglance/');

$yiit   = dirname(__FILE__).'/../../framework/yiit.php';
$config = dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/EcTestCase.php');

Yii::createWebApplication($config);