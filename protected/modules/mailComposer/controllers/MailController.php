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
                'actions' => array('display'),
                'users'   => array('@'),
            ),
            array('allow',
                'actions' => array('test'),
                'users'   => array('admin'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }
    
    /**
     * TEST ACTION
     * @return null
     */
    public function actionTest()
    {
        //$invite = EventInvite::model()->findByPk(650);
        //echo MailComposerModule::getMessage('newInvite', array('invite' => $invite));
        
        $customerInvite = CustomerInvite::model()->findByPk(3);
        echo MailComposerModule::getSubject('customerInvite', array('customerInvite' => $customerInvite));
        echo MailComposerModule::getMessage('customerInvite', array('customerInvite' => $customerInvite));
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
     * Creates a widget and initializes it.
     * This method first creates the specified widget instance.
     * It then configures the widget's properties with the given initial values.
     * At the end it calls {@link CWidget::init} to initialize the widget.
     * Starting from version 1.1, if a {@link CWidgetFactory widget factory} is enabled,
     * this method will use the factory to create the widget, instead.
     * @param string $className class name (can be in path alias format)
     * @param array $properties initial property values
     * @return CWidget the fully initialized widget instance.
     */
    public function createWidget($className, $properties=array())
    {
        if ( isset(Yii::app()->controller) )
        {
            return parent::createWidget($className, $properties);
        }
        
        // приложение запущено из консоли - имитируем widgetFactory
        $className=Yii::import($className,true);
        $widget=new $className($this);
        foreach($properties as $name=>$value)
            $widget->$name=$value;
        $widget->init();
        return $widget;
    }
}