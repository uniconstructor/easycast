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
     * 
     * @param  string $action - совершаемое действие (регистрация, приглашение, и т. п.)
     * @param  array $params - параметры для выполнения операции
     * @return string - тема письма
     */
    public static function getSubject($action, $params=null)
    {
        $mailComposer = self::getMailComposerComponent();
        switch ( $action )
        {
            // письмо с приглашением на съемки
            case 'newInvite':
                if ( ! isset($params['invite']) )
                {
                    throw new CException('Invite for mail subject is not set');
                }
                $invite = $params['invite'];
                return $mailComposer->createNewInviteMailSubject($invite);
            break;
            // приглашение на отбор актеров (для заказчика)
            case 'customerInvite':
                if ( ! isset($params['customerInvite']) )
                {
                    throw new CException('customerInvite for mail subject is not set');
                }
                $customerInvite = $params['customerInvite'];
                return $mailComposer->createCustomerInviteMailSubject($customerInvite);
            break;
            // @deprecated Регистрация через форму подачи заявки для топ-моделей
            case 'TMRegistration': 
                return 'Ваша заявка на участие в проекте "Топ-модель по-русски" направлена на рассмотрение';
            break;
        }
    }
    
    /**
     * Получить html-код письма, в зависимости от действия
     * 
     * @param  string $action - совершаемое действие (регистрация, приглашение, и т. п.)
     * @param  array  $params - параметры для выполнения операции
     * @return string - html-код тела письма
     */
    public static function getMessage($action, $params=null)
    {
        $mailOptions = array();
        if ( isset($params['mailOptions']) )
        {
            $mailOptions = $params['mailOptions'];
        }
        /* @var $mailComposer MailComposer */
        $mailComposer   = self::getMailComposerComponent();
        $moduleInstance = Yii::app()->getModule('mailComposer');
        
        switch ( $action )
        {
            // письмо с заданным вручную содержанием
            case 'customEmail':
                if ( ! isset($params['sourceModel']) OR ! $sourceModel = $params['sourceModel'] )
                {// не передан обязательный параметр для составления сообщения
                    throw new CException('Не передана модель с текстом письма');
                }
                if ( $configValue = $sourceModel->getConfig('customEmailText') )
                {// настройка в содержит текст письма
                    return $moduleInstance->createSimpleMessage(null, $configValue->value);
                }
            break;
            // письмо с приглашением на съемки
            // @todo проверить что поведение config подключено прежде чем обращаться к его методам
            case 'newInvite':
                if ( ! isset($params['invite']) )
                {// не передан обязательный параметр для составления 
                    throw new CException('Invite for mail is not set');
                }else
                {/* @var $invite EventInvite */
                    $invite = $params['invite'];
                }
                
                // если в настройках мероприятия указан другой текст оповещения
                $config = Config::model()->forModel($invite->event)->
                    withName('newInviteMailText')->find();
                if ( $config AND isset($config->value) AND $config->value )
                {
                    $configValue = $invite->event->getConfig('newInviteMailText');
                    // настройка в мероприятии содержит другой текст оповещения -
                    // в этом случае составляем другое письмо
                    $block  = array();
                    $button = array();
                    // ссылка на подачу заявки по токену
                    $button['caption'] = 'Подать заявку';
                    $button['link']    = Yii::app()->createAbsoluteUrl('/projects/invite/subscribe',
                        array('id' => $invite->id, 'key' => $invite->subscribekey));;
                    // кнопка попадает в блок
                    $block['button']   = $button;
                    // добавляем кнопку как сегмент письма 
                    $options = array('segments' => array($block));
                    // добавляем кнопку подачи заявки в конец письма
                    return $moduleInstance->createSimpleMessage(null, $configValue->value, $options);
                }else
                {// используем оригинальный текст приглашения
                    return $mailComposer->createNewInviteMailText($invite, $mailOptions);
                }
            break;
            // письмо с подтверждением заявки
            case 'approveMember':
                if ( ! isset($params['projectMember']) )
                {
                    throw new CException('projectMember for mail is not set');
                }
                $projectMember = $params['projectMember'];
                return $mailComposer->createApproveMemberMailText($projectMember, $mailOptions);
            break;
            // письмо с отклонением заявки
            case 'rejectMember':
                if ( ! isset($params['projectMember']) )
                {
                    throw new CException('projectMember for mail is not set');
                }
                $projectMember = $params['projectMember'];
                return $mailComposer->createRejectMemberMailText($projectMember, $mailOptions);
            break;
            // письмо с предварительным одобрением заявки
            case 'pendingMember':
                if ( ! isset($params['projectMember']) )
                {
                    throw new CException('projectMember for mail is not set');
                }
                $projectMember = $params['projectMember'];
                return $mailComposer->createPendingMemberMailText($projectMember, $mailOptions);
            break;
            // приглашение на отбор актеров (для заказчика)
            case 'customerInvite':
                if ( ! isset($params['customerInvite']) )
                {
                    throw new CException('customerInvite for mail is not set');
                }
                $customerInvite = $params['customerInvite'];
                return $mailComposer->createCustomerInviteMailText($customerInvite, $mailOptions);
            break;
            // @deprecated приглашение активировать анкету для участников из базы Светланы Строиловой
            case 'SSInvite':
                if ( ! isset($params['questionary']) )
                {
                    throw new CException('questionary for mail is not set');
                }
                return $mailComposer->createSSInviteMailText($params['questionary']);
            break;
            // приглашение активировать анкету для участников из нашей базы, при создании анкеты админом
            case 'ECRegistration':
                if ( ! isset($params['questionary']) )
                {
                    throw new CException('questionary for mail is not set');
                }
                return $mailComposer->createECRegistrationMailText($params['questionary']);
            break;
            // вызывной лист
            case 'callList':
                $addContacts = false;
                if ( ! isset($params['callList']) )
                {
                    throw new CException('callList for mail is not set');
                }
                if ( isset($params['addContacts']) AND $params['addContacts'] )
                {
                    $addContacts = true;
                }
                return $mailComposer->createCallListMailText($params['callList'], $addContacts);
            break;
            // кастинг-лист
            case 'castingList':
                $addContacts = false;
                if ( ! isset($params['castingList']) )
                {
                    throw new CException('callList for mail is not set');
                }
                if ( isset($params['addContacts']) AND $params['addContacts'] )
                {
                    $addContacts = true;
                }
                return $mailComposer->createCastingListMailText($params['castingList'], $addContacts);
            break;
            // коммерческое предложение
            case 'offer':
                $manager = null;
                if ( ! isset($params['offer']) )
                {
                    throw new CException('offer for mail is not set');
                }
                if ( isset($params['manager']) )
                {
                    $manager = $params['manager'];
                }
                return $mailComposer->createOfferMailText($params['offer'], $manager);
            break;
            // заказ, онлайн-кастинг или расчет стоимости
            case 'newOrder':
                if ( ! isset($params['order']) )
                {
                    throw new CException('Не указан заказ для составления письма');
                }
                if ( ! isset($params['target']) )
                {
                    throw new CException('Не указано для кого составлять письмо (заказчик или команда)');
                } 
                return $mailComposer->createOrderMailText($params['order'], $params['target']);
            break;
            // @deprecated подтверждение регистрации через подачу заявки на проект "топ-модель по-русски"
            case 'TMRegistration':
                if ( ! isset($params['questionary']) )
                {
                    throw new CException('questionary for mail is not set');
                }
                return $mailComposer->createTMRegistrationMailText($params['questionary']);
            break;
            // @todo
            /*case 'VacancyRegistration':
                if ( ! isset($params['questionary']) )
                {
                    throw new CException('questionary for mail is not set');
                }
                return $mailComposer->createVacancyRegistrationMailText($params['questionary']);
            break;*/
            // @deprecated подтверждение регистрации через подачу заявки на проект "МастерШеф"
            case 'MCRegistration':
                if ( ! isset($params['questionary']) )
                {
                    throw new CException('questionary for mail is not set');
                }
                return $mailComposer->createMCRegistrationMailText($params['questionary'], $params['vacancy'], $params['password']);
            break;
        }
    }
    
    /**
     * Создать письмо с самой простой структурой: заголовок и текст
     *
     * @param string $header
     * @param string $text
     * @param array $options
     * @return string
     */
    public function createSimpleMessage($header, $text, $options=array())
    {
        $mailComposer = self::getMailComposerComponent();
        return $mailComposer->createSimpleMail($header, $text, $options);
    }
    
    /**
     * Получить компонент, составляющий письма
     * 
     * @return MailComposer
     */
    protected static function getMailComposerComponent()
    {
        $config = array(
            'class' => 'application.modules.mailComposer.components.MailComposer',
        );
        $component = Yii::createComponent($config);
        if ( ! $component->getIsInitialized() )
        {
            $component->init();
        }
        return $component;
    }
    
    /**
     * 
     * 
     * @param $str
     * @param $params
     * @param $dic
     * @return string
     */
    public static function t($str='', $params=array(), $dic='mailComposer')
    {
        if ( Yii::t("MailComposerModule", $str) === $str )
        {
            return Yii::t("MailComposerModule.".$dic, $str, $params);
        }else
        {
            return Yii::t("MailComposerModule", $str, $params);
        }
    }
} 