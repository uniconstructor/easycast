<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('environments', dirname(dirname(__DIR__)) . '/environments');
Yii::setAlias('vendor', dirname(dirname(__DIR__)) . '/vendor');

// подключаем cockpit CMS
define('COCKPIT_CONFIG_PATH', \Yii::getAlias('@environments/'.YII_ENV.'/backend/config/cockpit.php'));
if ( ! defined('COCKPIT_EMBEDED') )
{
    require(\Yii::getAlias('@vendor/aheinze/cockpit/bootstrap.php'));
    // @todo вынести в отдельный модуль
    $pageCollection = cockpit('collections:get_collection_by_slug', 'yii-controller-actions');
    if ( $pageCollection AND $actions = $pageCollection->find()->toArray() )
    {
        $customUrlRules = array();
        foreach ( $actions as $action )
        {
            if ( isset($action['path']) AND $action['path'] )
            {
                \Yii::$app->urlManager->rules[$action['path']] = 'page/'.$action['name'];
            }
        }
    }
}
