<?php

/**
 * Модуль "Отчеты"
 */
class ReportsModule extends CWebModule
{
    /*
    public $defaultController = 'reports';

    public $controllerMap = array(
        'index' => array(
            'class' => 'reports.controllers.ReportsController',
        ),
    );*/

    /**
     * @param $str
     * @param $params
     * @param $dic
     * @return string
    */
    public static function t($str='', $params=array(), $dic='reports')
    {
        if ( Yii::t("ReportsModule", $str) == $str )
        {
            return Yii::t("ReportsModule.".$dic, $str, $params);
        }else
        {
            return Yii::t("ReportsModule", $str, $params);
        }
    }
}