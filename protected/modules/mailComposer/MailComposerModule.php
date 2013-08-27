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
        list($controller, $route) = Yii::app()->createController('/mailComposer/mail');
        switch ( $action )
        {
            // письмо с приглашением на съемки
            case 'newInvite':
                if ( ! isset($params['invite']) )
                {
                    throw new CException('Invite for mail subject is not set');
                }
                $invite = $params['invite'];
                return $controller->createNewInviteMailSubject($invite);
            break;
        }
    }
    
    /**
     * Получить html-код письма, в зависимости от действия
     * @param string $action - совершаемое действие (регистрация, приглашение, и т. п.)
     * @param array $params - параметры для выполнения операции
     * @return string - html-код тела письма
     */
    public static function getMessage($action, $params=null)
    {
        list($controller, $route) = Yii::app()->createController('/mailComposer/mail');
        switch ( $action )
        {
            // письмо с приглашением на съемки
            case 'newInvite': 
                $mailOptions = array();
                if ( isset($params['mailOptions']) )
                {
                    $mailOptions = $params['mailOptions'];
                }
                if ( ! isset($params['invite']) )
                {
                    throw new CException('Invite for mail is not set');
                }
                $invite = $params['invite'];
                return $controller->createNewInviteMailText($invite, $mailOptions); 
            break;
            // письмо с подтверждением заявки
            case 'approveMember':
                $mailOptions = array();
                if ( isset($params['mailOptions']) )
                {
                    $mailOptions = $params['mailOptions'];
                }
                if ( ! isset($params['projectMember']) )
                {
                    throw new CException('projectMember for mail is not set');
                }
                $projectMember = $params['projectMember'];
                return $controller->createApproveMemberMailText($projectMember, $mailOptions);
            break;
            // письмо с отклонением заявки
            case 'rejectMember':
                $mailOptions = array();
                if ( isset($params['mailOptions']) )
                {
                    $mailOptions = $params['mailOptions'];
                }
                if ( ! isset($params['projectMember']) )
                {
                    throw new CException('projectMember for mail is not set');
                }
                $projectMember = $params['projectMember'];
                return $controller->createRejectMemberMailText($projectMember, $mailOptions);
            break;
        }
    }
    
    /**
     * Создать письмо с самой простой структурой: заголовок и текст
     * @param string $header
     * @param string $text
     * @param array $options
     * @return string
     */
    public function createSimpleMessage($header, $text, $options=array())
    {
        list($controller, $route) = Yii::app()->createController('/mailComposer/mail');
        return $controller->createSimpleMail($header, $text, $options);
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