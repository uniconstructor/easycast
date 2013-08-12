<?php

/**
 * Модуль для составления и верстки писем
 * Составляет красиво сверстанное письмо из фрагментов HTML. Письма открываются и нормально выглядят
 * в любом браузере или почтовом клиенте (в том числе через веб-интерфейс)
 */
class MailComposerModule extends CWebModule
{
    /**
     * @var string - используемый по умолчанию контроллер
     */
    public $defaultController = 'mail';
    
    /**
     * Получить тему письма, в зависимости от действия
     * @param string $action - совершаемое действие (регистрация, приглашение, и т. п.)
     * @param array $params - параметры для выполнения операции
     * @return string - тема письма
     */
    public static function getSubject($action, $params=null)
    {
        
    }
    
    /**
     * Получить html-код письма, в зависимости от действия
     * @param string $action - совершаемое действие (регистрация, приглашение, и т. п.)
     * @param array $params - параметры для выполнения операции
     * @return string - html-код тела письма
     */
    public static function getMessage($action, $params=null)
    {
        
    }
    
    /**
     * @param $str
     * @param $params
     * @param $dic
     * @return string
     */
    public static function t($str='',$params=array(),$dic='calendar') {
        if (Yii::t("MailComposer", $str)==$str)
        {
            return Yii::t("MailComposer.".$dic, $str, $params);
        }else
       {
            return Yii::t("MailComposer", $str, $params);
        }
    }
} 