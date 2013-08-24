<?php

/**
 * Контроллер, составляющий все письма сайта
 * 
 * @todo перенести действие test в функциональные тесты
 * @todo языковые строки
 */
class MailController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    //public $layout = 'application.modules.mailComposer.views.layouts.mail';
    
    /**
     * (non-PHPdoc)
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('application.modules.projects.models.*');
        parent::init();
    }
    
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }
    
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     *
     * @todo настроить проверку прав на основе ролей
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('createSimpleMail', 'display', 'test'),
                'users'   => array('@'),
            ),
            array('allow',
                'actions' => array('test'),
                'users'   => array('admin'),
            ),
            /*array('deny',
                'users' => array('*'),
            ),*/
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see CController::behaviors()
     */
    public function behaviors()
    {
        return array(
            // функции создания писем для модуля "проекты"
            'ProjectMailsBehavior' => array(
                'class' => 'application.modules.mailComposer.controllers.behaviors.ProjectMailsBehavior',
            ),
        );
    }
    
    /**
     * TEST ACTION
     * @return null
     */
    public function actionTest()
    {
        $invite = EventInvite::model()->findByPk(650);
        
        echo $this->createNewInviteMailText($invite);
    }
    
    /**
     * Отобразить веб-версию письма, пришедшего на почту
     * 
     * @return null
     * @todo переписать
     */
    public function actionDisplay()
    {
        $segments = array();
        $segments[] = array(
            'type' => 'textOnly',
            'header' => 'Заголовок текста',
            'text' => 'Сам текст. С <b>разнообразным</b> <i>Форматированием</i><p>И абзацами</p>',
        );
        $segments[] = array(
            'header' => 'ЕЩЕ ЗАГОЛОВОК',
            'text' => 'Сам текст 2. С <b>разнообразным 2</b> <i>Форматированием 2</i><p>И абзацами</p>',
            'button' => array('link' => '#', 'caption' => 'Подать заявку'),
        );
        $this->widget('application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler',
            array(
                'mainHeader' => 'Test mail subject!',
                'segments'   => $segments,
                'signature'  => 'Have a nice day.<br>Goodbye.',
            )
        );
    }
    
    /**
     * Создать самое простое письмо: заголовок, подзаголовок, абзац текста, 
     * стандартная подпись с контактами, отписка по желанию. Все настраивается.
     * 
     * @param array $params - массив параметров для составления письма
     * @return string - html-код письма
     * 
     * @todo пока только заготовка
     */
    public function createSimpleMail($header, $text, $options=array())
    {
        $defaults = $this->getMailDefaults();
        $options = CMap::mergeArray($defaults, $options);
        
        // составляем текст письма
        $block = array(
            'header' => $header,
            'text'   => $text,
        );
        
        // добавляем все блоки с информацией в массив настроек для виджета EMailAssembler
        $options['segments'] = array($block);
        // создаем виджет и получаем из него полный HTML-код письма
        return $this->owner->widget('application.modules.mailComposer.extensions.widgets.EMailAssembler.EMailAssembler',
            $options, true);
    }
    
    /**
     * Получить настройки по умолчанию для составления простого письма
     * @return array
     */
    protected function getSimpleMailDefaults()
    {
        return array(
            // заголовок первого абзаца
            'header' => '',
            // первый абзац текста (только текст, html и самая простая разметка)
            'text'    => '',
            // настройки для виджета, собирающего письмо из блоков
            'assemblerOptions' => array(),
        );
    }
    
    /**
     * Получить настройки по умолчанию для виджета EMailAssembler
     * 
     * @return array
     * 
     * @todo брать настройки из специального плагина
     * @todo а может вообще удалить если не пригодится
     */
    public function getMailDefaults()
    {
        return array(
            'showTopServiceLinks'    => false,
            'showBottomServiceLinks' => false,
            'showSocialButtons'      => false,
            'showContactPhone'       => true,
            'showContactEmail'       => true,
            'contactPhone'           => Yii::app()->params['adminPhone'],
            'contactEmail'           => Yii::app()->params['adminEmail'],
            'mainHeader'             => '',
            'segments'               => array(),
            'signature'              => '',
            'showFeedbackNotification' => true,
            'showPasswordNotification' => false,
            'userHasFirstAccess'       => true,
        );
    }
}