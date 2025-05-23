<?php

/**
 * Письмо, составляемое вручную
 * 
 * @todo проверки входных данных при запуске
 * @todo сейчас быстрее использовать Yii::app()->getModule('mailComposer')->createSimpleMessage()
 *       работа над виджетом временно приостановлена
 */
class EMailSimpleInvite extends EMailBase
{
    /**
     * @var CActiveRecord - модель, настройки которой будут использоваться для составления письма 
     *                      Обязательно должна иметь настройку 'newInviteMailText'
     */
    public $sourceModel;
    /**
     * @var EventInvite - приглашение на мероприятие
     */
    public $invite;
    /**
     * @var string - имя пользователя для обращения в письме
     */
    public $name;
    /**
     * @var string - ссылка-ключ для автоматической авторизации по токену
     *               Используется для приглашений (как участников так и заказчиков)
     *               Содержит все необходимые параметры
     */
    public $subscribeUrl;
    /**
     * @var string - 
     *             invite
     *             registration
     *             pending
     *             approved
     *             rejected
     */
    //public $messageType;
    /**
     * @var Project
     */
    //public $project;
    /**
     * @var ProjectEvent
     */
    //public $event;
    /**
     * @var EventVacancy
     */
    //public $vacancy;
    /**
     * @var Questionary
     */
    //public $questionary;
    
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var string
     */
    protected $pathPrefix = 'application.modules.mailComposer.extensions.widgets.';
    
    /**
     * @see EMailBase::init()
     */
    public function init()
    {
        parent::init();
        // получаем текст письма из настроек модели
        $this->config = $this->sourceModel->getConfigObject('newInviteMailText');
        // ссылка на подачу заявки
        if ( $this->invite )
        {// через токен
            $this->subscribeUrl = Yii::app()->createAbsoluteUrl('/projects/invite/subscribe',
                array('id' => $this->invite->id, 'key' => $this->invite->subscribekey));
        }else
        {// на динамическую форму
            $this->subscribeUrl = Yii::app()->createAbsoluteUrl('/projects/vacancy/registration',
                array('vid' => $this->model->objectid));
        }
    }

    /**
     * @see EMailBase::run()
     */
    public function run()
    {
        // приветствие
        $this->textBlock($this->createGreeting());
        // получаем текст письма из настройки
        $this->textBlock($this->config->value);
        // добавляем в конце кнопке подачи заявки
        $this->textBlock($this->createInviteButton());
        
        // в конце выводим виджет со всеми данными
        parent::run();
    }
    
    /**
     * Получить последний блок письма с приглашением на съемки - с предложением и кнопкой подачи заявки
     * 
     * @param EventInvite $event
     * @return array
     */
    protected function createInviteButton()
    {
        $block  = array();
        $button = array();
        // кнопка с приглашением
        $button['caption'] = 'Подать заявку';
        $button['link']    = $this->subscribeUrl;
        // добавляем кнопку в блок
        $block['button']   = $button;
        
        return $block;
    }
}