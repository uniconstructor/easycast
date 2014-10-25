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
        $baseFilters = parent::filters();
	    $newFilters  = array(
	        'accessControl',
	    );
	    return CMap::mergeArray($baseFilters, $newFilters);
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
                'roles'   => array('*'),
            ),
            array('allow',
                'actions' => array('webVersion'),
                'roles'   => array('admin'),
            ),
            array('allow',
                'actions' => array('emailPreview'),
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
     * 
     * @return null
     * 
     * @deprecated использовать actionWebVersion()
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
        }elseif ( $type == 'newInvite' )
        {
            $invite = EventInvite::model()->findByPk($id);
            echo MailComposerModule::getMessage('newInvite', array(
                'invite'  => $invite,
            ));
        }elseif ( $type == 'castingList' )
        {
            $castingList = RCallList::model()->findByPk($id);
            echo MailComposerModule::getMessage('castingList', array('castingList' => $castingList));
        }
    }
    
    /**
     * Отобразить веб-версию письма, пришедшего на почту
     * 
     * @return null
     * 
     * @deprecated использовать actionWebVersion() 
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
        if ( $type == 'castingList' )
        {
            Yii::import('reports.models.*');
            if ( ! $castingList = RCallList::model()->findByPk($id) )
            {
                throw new CHttpException('404', 'Страница не найдена');
            }
            if ( $castingList->key != $key AND ! Yii::app()->user->checkAccess('Admin') )
            {
                throw new CHttpException('404', 'Страница не найдена');
            }
            echo MailComposerModule::getMessage('castingList', array('castingList' => $castingList));
        }
    }
    
    /**
     * Отобразить веб-версию письма, пришедшего на почту
     * Это действие используется для предварительного просмотра 
     * отправляемых оповещений, а также для тестирования отправляемых писем
     * Используется для всех стандартных оповещений системы
     * 
     * @return void
     * 
     * @todo проверить action по списку допустимых значений
     */
    public function actionWebVersion()
    {
        // тип стандартного оповещения
        $action = Yii::app()->request->getParam('action');
        // параметры для создания оповещения (в зависимости от типа)
        $params = $this->getMessageParams($action);
        // выводим письмо
        echo MailComposerModule::getMessage($action, $params);
    }
    
    /**
     * Creates a widget and initializes it.
     * This method first creates the specified widget instance.
     * It then configures the widget's properties with the given initial values.
     * At the end it calls {@link CWidget::init} to initialize the widget.
     * Starting from version 1.1, if a {@link CWidgetFactory widget factory} is enabled,
     * this method will use the factory to create the widget, instead.
     * 
     * @param  string $className class name (can be in path alias format)
     * @param  array $properties initial property values
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
    
    /**
     * Получить параметры составления письма со стандартным оповещением
     * Используется для того чтобы посмотреть в админке любое письмо 
     * по любому поводу перед отправкой
     * 
     * @param  string $type
     * @param  int $id
     * @return array
     * 
     * @todo брать список стандартных оповещений из настройки
     * @todo документировать все варианты
     * @todo добавить проверку ключей
     */
    protected function getMessageParams($action)
    {
        // параметры для составления письма
        $params = array();
        // id модели на основании которой составлялось оповещение
        $id  = Yii::app()->request->getParam('id');
        // токен доступа (если требуется)
        $key = Yii::app()->request->getParam('key');
        switch ( $action )
        {
            case 'customEmail':
                // @todo отправка писем с произвольным содержанием без привязке к системным событиям
                //       пока еще не реализована
                //$params['sourceModel'] = ???;
            break;
            case 'newInvite':
                $params['invite'] = EventInvite::model()->findByPk($id);
            break;
            case 'approveMember':
                $params['projectMember'] = ProjectMember::model()->findByPk($id);
            break;
            case 'rejectMember':
                $params['projectMember'] = ProjectMember::model()->findByPk($id);
            break;
            case 'pendingMember':
                $params['projectMember'] = ProjectMember::model()->findByPk($id);
            break;
            case 'customerInvite':
                $params['customerInvite'] = CustomerInvite::model()->findByPk($id);
            break;
            case 'SSInvite':
                $params['questionary'] = Questionary::model()->findByPk($id);
            break;
            case 'ECRegistration':
                $params['questionary'] = Questionary::model()->findByPk($id);
            break;
            case 'callList':
                $params['addContacts'] = Yii::app()->request->getParam('addContacts');
                $params['callList']    = RCallList::model()->findByPk($id);
            break;
            case 'castingList':
                $params['addContacts'] = Yii::app()->request->getParam('addContacts');
                $params['castingList'] = RCallList::model()->findByPk($id);
            break;
            case 'offer':
                $params['offer']   = CustomerOffer::model()->findByPk($id);
                $params['manager'] = User::model()->findByPk($id);
            break;
            case 'newOrder':
                $params['order']  = FastOrder::model()->findByPk($id);
                $params['target'] = Yii::app()->request->getParam('target');
            break;
            // @deprecated
            //case 'TMRegistration': break;
            case 'MCRegistration':
                $params['questionary'] = Questionary::model()->findByPk($id);
                $params['vacancy']     = EventVacancy::model()->findByPk($id);
                $params['password']    = Yii::app()->request->getParam('password');
            break;
        }
        return $params;
    }
}