<?php

/**
 * Модуль "Календарь событий"
 * Отображает все предстоящие съемки и кастинги в которых может учавствовать пользователь
 */
class CalendarModule extends CWebModule
{
    /**
     * @var CDbCriteria - условия по которым будут выбираться события в календаре
     */
    public $criteria;
    
    public $defaultController = 'calendar';
    
    public $controllerMap = array(
        'index' => array(
                'class'=>'application.modules.calendar.controllers.CalendarController',
            ),
        );
    
    /**
     * @param $str
     * @param $params
     * @param $dic
     * @return string
     */
    public static function t($str='',$params=array(),$dic='calendar') {
        if (Yii::t("CalendarModule", $str)==$str)
        {
            return Yii::t("CalendarModule.".$dic, $str, $params);
        }else
       {
            return Yii::t("CalendarModule", $str, $params);
        }
    }
} 