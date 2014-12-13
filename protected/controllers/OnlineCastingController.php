<?php

/**
 * Контроллер для LP перед созданием онлайн-кастинга
 * 
 * @TODO добавить оплату при создании роли в онлайн-кастинге
 * @todo переименовать статистов в "типажи"
 * @todo настроить права доступа
 */
class OnlineCastingController extends Controller
{
    /**
     * @var string - верстка всех страниц онлайн-кастинга - в одну колонку
     */
    public $layout = '//layouts/column1';
    
    /**
     * @see CController::init()
     */
    public function init()
    {
        Yii::import('ext.LPNValidator.LPNValidator');
        // подключаем необходимые для поиска по каталогу классы
        Yii::import('projects.models.*');
        Yii::import('catalog.models.*');
    }
    
    /**
     * @return array
     *
     * @todo настроить проверку прав на основе RBAC
     */
    public function filters()
    {
        $baseFilters = parent::filters();
        $newFilters  = array(
            // фильтр для подключения YiiBooster 3.x (bootstrap 2.x)
            array(
                'ext.bootstrap.filters.BootstrapFilter - count, saveRoleCriteria, clearRoleCriteria',
            ),
        );
        return CMap::mergeArray($baseFilters, $newFilters);
    }
    
    /**
     * Отобразить первую страницу с пояснением о начале работы с онлайн-кастингом
     * 
     * @return void
     */
    public function actionIndex()
    {
        $this->render('index');
    }
    
    /**
     * 
     * @return void
     */
    public function actionCreate()
    {
        $onlineCastingForm     = OnlineCastingForm::getCastingInfo();
        $onlineCastingRoleForm = OnlineCastingForm::getRoleInfo();
        
        // AJAX-проверка формы создания онлайн-кастинга
        // (должна происходить до получения данных из сессии)
        $this->performAjaxValidation($onlineCastingForm);
        $this->performAjaxValidation($onlineCastingRoleForm);
        
        
        if ( $castingFormData = Yii::app()->request->getPost('OnlineCastingForm') )
        {// сохранена форма кастинга
            $onlineCastingForm->attributes = $castingFormData;
            if ( $onlineCastingForm->validate() )
            {// проверяем данные и сохраняем их в сессию
                
                $onlineCastingForm->save();
                // переходим к следующему шагу
                $url = Yii::app()->createUrl('/onlineCasting/create', array('step' => 'roles'));
                $this->redirect($url);
            }
        }
        if ( $roleFormData = Yii::app()->request->getPost('OnlineCastingRoleForm') )
        {// сохранена форма роли
            $onlineCastingRoleForm->attributes = $roleFormData;
            if ( $onlineCastingRoleForm->validate() )
            {
                $onlineCastingRoleForm->save();
                // переходим к следующему шагу
                $url = Yii::app()->createUrl('/onlineCasting/create', array('step' => 'finish'));
                $this->redirect($url);
            }
        }
        
        // текущий шаг создания онлайн-кастинга
        // по умолчанию - первый шаг, ввод информации о проекте и мероприятии 
        $step = Yii::app()->request->getParam('step', 'info');
        
        $this->render('create', array(
            'onlineCastingForm'     => $onlineCastingForm,
            'onlineCastingRoleForm' => $onlineCastingRoleForm,
            'step'                  => $step,
        ));
    }
    
    /**
     * Сохранить информацию об онлайн-кастинге и показать финальную страницу с заключительными словами
     * @return void
     */
    public function actionConclusion()
    {
        // сохраняем данные в базу
        if ( Yii::app()->session->contains('onlineCasting') )
        {
            $this->finalizeCasting();
        }else
        {
            throw new CHttpException('500', 'Произошла ошибка: 
                Не удалось найти введенные данные онлайн-кастинга в сессии. 
                Отчет отправлен в нашу техническую службу, пожалуйста обратитесь к нашему оператору
                (онлайн-помощь внизу экрана) чтобы мы все равно смогли создать для вас кастинг несмотря ни на что :)');
        }
        
        $this->render('conclusion');
    }
    
    /**
     * Подсчитать количество подходящих для кастинга участников, учитывая текущие критерии поиска 
     * 
     * @return void
     */
    public function actionCount()
    {
        Yii::import('questionary.models.*');
        $data = CJSON::decode(Yii::app()->request->getPost('data'));
        
        if ( ! $data OR empty($data) )
        {
            return;
        }
        // @todo брать список фильтров поиска из специального набора фильтров для онлайн-кастинга
        //       а не из раздела "вся база"
        $rootSection = CatalogSection::model()->findByPk(1);
        
        $criteria = CatalogModule::createSearchCriteria($data, $rootSection->searchFilters);
        echo Questionary::model()->count($criteria);
        
        Yii::app()->end();
    }
    
    /**
     * Сохранить критерии поиска для роли
     * @return void
     */
    public function actionSaveRoleCriteria()
    {
        if ( ! Yii::app()->request->isAjaxRequest )
        {
            Yii::app()->end();
        }
        if ( $data = Yii::app()->request->getPost('data', null) AND ! empty($data) )
        {// переданы данные для поиска - делаем из них нормальный массив
            $data = CJSON::decode($data);
        }
        OnlineCastingForm::setRoleCriteria($data);
    }
    
    /**
     * Очистить критерии поиска для роли
     * @return void
     */
    public function actionClearRoleCriteria()
    {
        OnlineCastingForm::setRoleCriteria(array());
    }
    
    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if ( Yii::app()->request->getParam('ajax') === 'online-casting-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        if ( Yii::app()->request->getParam('ajax') === 'online-casting-role-form' )
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
    
    /**
     * Окончательно сохранить онлайн-кастинг в базу, и оповестить команду о новом заказе
     * Удаляет из сессии данные кастинга если все прошло успешно
     * @throws CException
     * @return boolean
     * 
     * @todo оповестить команду в Мегаплане
     * @todo создать заказ
     * @todo определить кого назначать руководителем проекта по умолчанию
     * @todo сохранить критерии поиска
     */
    protected function finalizeCasting()
    {
        // получаем данные всех форм из сессии
        /* @var $template OnlineCastingForm */
        $template     = OnlineCastingForm::getCastingInfo();
        $roleTemplate = OnlineCastingForm::getRoleInfo();
        $searchData   = OnlineCastingForm::getRoleCriteria();
        
        // сохраняем информацию о проекте
        $project = new Project();
        $project->name        = $template->projectname;
        $project->type        = $template->projecttype;
        $project->description = $template->projectdescription;
        $project->notimestart = 1;
        $project->notimeend   = 1;
        $project->leaderid    = 1;
        // помечаем проект как онлайн-кастинг
        $project->virtual     = 1;
        if ( ! $project->save() )
        {
            throw new CHttpException(500, 'Не удалось сохранить проект онлайн-кастинга. '.
                $this->getCustomerErrorMessage());
        }
        
        // сохраняем информацию о мероприятии и привызываем мероприятие к проекту
        $event   = new ProjectEvent();
        $event->projectid   = $project->id;
        $event->name        = $template->projectname;
        $event->description = $template->eventdescription;
        $event->virtual     = 1;
        $event->type        = ProjectEvent::TYPE_CASTING;
        if ( $template->plandate )
        {// у кастинга есть планируемая дата проведения
            $event->timestart = CDateTimeParser::parse($template->plandate, Yii::app()->params['inputDateFormat']);
        }else
        {// кастинг без определенной даты
            $event->timestart = 0;
            $event->nodates   = true;
        }
        if ( ! $event->save() )
        {
            throw new CHttpException(500, 'Не удалось сохранить съемочный день для онлайн-кастинга. '.
                $this->getCustomerErrorMessage());
        }
        
        // сохраняем роль
        $vacancy = new EventVacancy();
        $vacancy->eventid     = $event->id;
        $vacancy->name        = $roleTemplate->name;
        $vacancy->description = $roleTemplate->description;
        if ( (int)$roleTemplate->salary > 1 )
        {
            $vacancy->salary = $roleTemplate->salary;
        }
        if ( ! $vacancy->save() )
        {
            throw new CHttpException(500, 'Не удалось сохранить роль для онлайн-кастинга. '.
                $this->getCustomerErrorMessage());
        }
        // сохраняем критерии поиска роли
        $vacancy->setSearchData($searchData);
        
        // создаем заказ, который отправит сообщение команде (мегаплан + почта) и подтверждение заказчику
        $order = new FastOrder();
        $order->type       = FastOrder::TYPE_CASTING;
        $order->name       = $template->name;
        $order->customerid = 0;
        $order->email      = $template->email;
        $order->phone      = $template->phone;
        // дополнительные данные о созданном кастинге
        $orderData = array(
            'projectid'   => $project->id,
            'castingData' => $template->attributes,
            'roleData'    => $roleTemplate->attributes,
            'searchData'  => $searchData,
        );
        $order->orderdata  = serialize($orderData);
        if ( ! $order->save() )
        {
            throw new CException('Не удалось сохранить заявку на онлайн-кастинг. '.
                $this->getCustomerErrorMessage());
        }
        // привязываем проект к только что созданному заказу
        $project->orderid = $order->id;
        if ( ! $project->save(true, array('orderid')) )
        {// @todo не выбрасывать исключение. просто писать ошибку в лог
            throw new CException('Не удалось привязать заказу к проекту. '.
                $this->getCustomerErrorMessage());
        }
        // удаляем заготовку проекта онлайн-кастинга из сессии
        Yii::app()->session->remove('onlineCasting');
        
        return true;
    }
    
    /**
     * Если у заказчика вдруг что-то случилось, то обязательно просим его связаться с оператором,
     * чтобы не потерять заказ.
     * @return void
     */
    protected function getCustomerErrorMessage()
    {
        return 'Отчет отправлен в нашу техническую службу, пожалуйста обратитесь к нашему оператору
            (онлайн-помощь внизу экрана)';
    }
}