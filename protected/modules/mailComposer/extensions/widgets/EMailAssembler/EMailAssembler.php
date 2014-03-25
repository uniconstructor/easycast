<?php

/**
 * Виджет-сборщик письма из отдельных фрагментов
 * Вызывает другие виджеты чтобы вывести фрагменты письма
 * 
 * @todo автоматически добавлять ссылку с напоминанием пароля, если участник ни разу не заходил на сайт
 */
class EMailAssembler extends CWidget
{
    /**
     * @var bool - отображать ли служебные ссылки "отписаться/мои настройки/веб-версия"
     */
    public $showTopServiceLinks    = false;
    /**
     * @var bool - отображать ли служебные ссылки "отписаться/мои настройки/веб-версия"
     */
    public $showBottomServiceLinks = false;
    /**
     * @var bool - отоборажать ли кнопки соцсетей сверху в письме
     */
    public $showSocialButtons      = false;
    /**
     * @var bool - отображать ли контактный телефон
     */
    public $showContactPhone       = true;
    /**
     * @var bool - отображать ли контактный email
     */
    public $showContactEmail       = true;
    /**
     * @var string - контактный телефон в конце письма
     */
    public $contactPhone   = '';
    /**
     * @var string - контактный email в конце письма
     */
    public $contactEmail   = '';
    /**
     * @var string - главный заголовок всего письма
     */
    public $mainHeader     = '';
    /**
     * @var 'image'|'text' - тип отображения главного заголовка всего письма
     *                     text - просто заголовок h1
     *                     image - красивая фирменная шапка страницы
     */
    public $mainHeaderType = 'image';
    /**
     * @var bool - делать ли отступы для содержимого внутри письма
     */
    public $contentPadding = 30;
    /**
     * @var array - массив, состоящий из массивов настроек для виджета EMailContent
     */
    public $segments  = array();
    /**
     * @var string - дополнительный текст над стандартной подписью внизу письма
     */
    public $signature = '';
    /**
     * @var bool - показывать в подписи сообщение о том, что на письмо можно ответить
     */
    public $showFeedbackNotification = true;
    /**
     * @var bool - показывать в подписи напоминание о том как восстановить пароль
     */
    public $showPasswordNotification = false;
    /**
     * @var bool - этот параметр указывает, входил ли когда-нибудь участник, получивший это письмо на сайт или нет
     *             Если не входил - то нужно напомнить ему об этом, и предложить восстановить пароль
     *             (по умолчанию считаем, что участник хотя бы раз вродил на сайт и пароль помнит)
     */
    public $userHasFirstAccess       = true;
    /**
     * @var User
     */
    public $manager;
    /**
     * @var array - параметры отображения верхнего колонтитула письма (синяя полоска сверху которая)
     */
    public $topBarOptions = array();
    /**
     * @var string - для кого составляется письмо
     *               user - для участника
     *               customer - для заказчика
     */
    public $target = 'user';
    
    protected $_pluginsPrefix = 'application.modules.mailComposer.extensions.widgets.';
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        // определяем какие контакты показывать участникам рассылки
        if ( ! $this->contactEmail )
        {
            $this->contactEmail = Yii::app()->params['adminEmail'];
        }
        if ( ! $this->contactPhone )
        {
            $this->contactPhone = Yii::app()->params['adminPhone'];
        }
        
        if ( ! $this->showContactEmail )
        {
            $this->contactEmail = '';
        }
        if ( ! $this->showContactPhone )
        {
            $this->contactPhone = '';
        }
        
        // составляем подпись внизу письма
        $notes = array();
        if ( trim($this->signature) )
        {// нужно добавить к нашей стандартной подписи еще что-то сверху
            $notes[] = $this->signature;
        }
        if ( $this->showPasswordNotification )
        {// нужно напомнить участнику, что он может восстановить свой пароль
            $notes[] = $this->createSignaturePasswordNotice();
        }
        if ( $this->showFeedbackNotification )
        {// сообщаем участнику, что он может просто ответить на письмо для обратной связи
            $notes[] = $this->createSignatureFeedbackNotice();
        }
        // в конце всегда добавляем бессмысленную фразу, потому что кто-то когда-то решил что так принято
        $notes[] = $this->createRegardsText();
        // соединяем все сообщения в подписи в одно, разбивая их по строчкам
        $this->signature = implode('<br>', $notes);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('envelope');
    }
    
    /**
     * Отобразить верхнюю полоску письма
     * 
     * @return null
     */
    protected function displayTopBar()
    {
        $this->widget($this->_pluginsPrefix.'EMailTopBar.EMailTopBar', $this->topBarOptions);
    }
    
    /**
     * Отобразить большой заголовок для всего письма (если он нужен)
     * 
     * @return null
     */
    protected function displayMainHeader()
    {
        $this->widget($this->_pluginsPrefix.'EMailHeader.EMailHeader', array(
            'header' => $this->mainHeader,
            'type'   => $this->mainHeaderType,
            'target' => $this->target,
        ));
    }
    
    /**
     * Отобразить основное содержимое письма
     * 
     * @return null
     */
    protected function displayContent()
    {
        $this->widget($this->_pluginsPrefix.'EMailContent.EMailContent', array(
            'segments' => $this->segments,
            'padding'  => $this->contentPadding,
        ));
    }
    
    /**
     * Отобразить нижнюю часть письма - с подписью и контактами
     * 
     * @return null
     */
    protected function displayFooter()
    {
        $this->widget($this->_pluginsPrefix.'EMailFooter.EMailFooter', array(
            'contactEmail' => $this->contactEmail,
            'contactPhone' => $this->contactPhone,
            'signature'    => $this->signature,
        ));
    }
    
    /**
     * Получить стандартный текст подписи с указаниями по тому как задавать вопросы
     * @return string
     */
    protected function createSignatureFeedbackNotice()
    {
        $message = 'Если у вас есть вопросы - то вы можете задать их, ответив на это письмо';
        if ( $this->showContactPhone AND $this->contactPhone )
        {// оставляем возможность не указывать телефон для обычной рассылки
            $message .= ' или позвонив по телефону.<br> На сайте работает чат для онлайн-консультаций.';
        }else
        {
            $message .= ' или воспользоваться чатом для онлайн-консультации на нашем сайте.';
        }
        return $message;
    }
    
    /**
     * Получить текст со ссылкой для напоминания пароля
     * @return string
     */
    protected function createSignaturePasswordNotice()
    {
        $message = '';
        $passwordUrl = Yii::app()->createAbsoluteUrl('/user/recovery');
        if ( ! $this->userHasFirstAccess )
        {// участник сам ни разу не зашел на сайт - напомним ему об этом
            $message .= '<b>По нашим данным вы еще ни разу не авторизовались на сайте.</b> ';
            $message .= 'Если вы забыли пароль, или он не пришел вам при регистрации - его можно восстановить по этой ссылке: ';
        }else
        {// просто напоминаем что пароль всегда можно сменить
            $message .= 'Если вы хотите восстановить или сменить пароль от сайта - это можно сделать по ссылке: ';
        }
        $message .= CHtml::link($passwordUrl, $passwordUrl, array('target' => '_blank'));
        
        return $message;
    }
    
    /**
     * Получить стандартную подпись внизу письма
     * @return string
     */
    protected function createRegardsText()
    {
        if ( $this->manager AND $this->manager->questionary->firstname )
        {
            return "С уважением, {$this->manager->questionary->fullname}.";
        }
        return "С уважением, команда проекта EasyCast.";
    }
}