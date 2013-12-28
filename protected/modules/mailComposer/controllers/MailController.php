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
                'users'   => array('*'),
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
        Yii::import('reports.models.*');
        $type = Yii::app()->request->getParam('type');
        $subType = Yii::app()->request->getParam('subtype');
        $id   = Yii::app()->request->getParam('id');
        //$invite = EventInvite::model()->findByPk(650);
        //echo MailComposerModule::getMessage('newInvite', array('invite' => $invite));
        
        //$customerInvite = CustomerInvite::model()->findByPk(3);
        //echo MailComposerModule::getSubject('customerInvite', array('customerInvite' => $customerInvite));
        //echo MailComposerModule::getMessage('customerInvite', array('customerInvite' => $customerInvite));
        
        if ( $type == 'callList' )
        {
            $callList = RCallList::model()->findByPk($id);
            echo MailComposerModule::getMessage('callList', array('callList' => $callList));
        }elseif ( $type == 'offer' )
        {
            $offer = CustomerOffer::model()->findByPk($id);
            echo $offerMail = MailComposerModule::getMessage('offer', array('offer' => $offer));
            //UserModule::sendMail('frost@easycast.ru', 'test message', $offerMail, true);
        }elseif ( $type == 'SSInvite' )
        {
            $questionary = Questionary::model()->findByPk($id);
            echo MailComposerModule::getMessage('SSInvite', array('questionary' => $questionary));
        }elseif ( $type == 'newOrder' )
        {
            $order = FastOrder::model()->findByPk($id);
            echo MailComposerModule::getMessage('newOrder', array(
                'order'  => $order,
                'target' => $subType,
            ));
        }
    }
    
    /**
     * Отобразить веб-версию письма, пришедшего на почту
     * 
     * @return null
     * @todo переписать
     */
    public function actionDisplay()
    {
        $type = Yii::app()->request->getParam('type');
        $id   = Yii::app()->request->getParam('id');
        $key  = Yii::app()->request->getParam('key');
        
        if ( $type == 'callList' )
        {
            Yii::import('reports.models.*');
            
            if ( ! $callList = RCallList::model()->findByPk($id) )
            {
                throw new CHttpException('404', 'Страница не найдена');
            }
            if ( $callList->key != $key AND ! Yii::app()->user->checkAccess('Admin') )
            {
                throw new CHttpException('404', 'Страница не найдена');
            }
            echo MailComposerModule::getMessage('callList', array('callList' => $callList));
        }
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