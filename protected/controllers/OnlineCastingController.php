<?php

/**
 * Контроллер для LP перед созданием онлайн-кастинга
 * 
 * @TODO добавить оплату при создании роли в онлайн-кастинге
 * @todo переименовать статистов в "типажи"
 */
class OnlineCastingController extends Controller
{
    /**
     * @var string - верстка всех страниц онлайн-кастинга - в одну колонку
     */
    public $layout='//layouts/column1';
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
     * Отобразить первую страницу с пояснением о начале работы с онлайн-кастингом
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
     * Сохранить информацию об онлайн-кастинге в сессию
     * @return void
     */
    /*public function actionSaveCasting()
    {
        
    }*/
    
    /**
     * Сохранить в сессию информацию о роли
     * @return void
     */
    /*public function actionSaveRole()
    {
        
    }*/
    
    /**
     * Сохранить кастинг со всеми ролями из сессии в базу и
     * отправить команде оповещение о новом запросе на кастинг
     * @return void
     */
    /*public function actionFinishCastingSetup()
     {
    
    }*/
    
    /**
     * Сохранить критерии поиска для роли
     * @return void
     */
    public function actionSaveRoleCriteria()
    {
        
    }
    
    /**
     * Очистить критерии поиска для роли
     * @return void
     */
    public function actionClearRoleCriteria()
    {
        
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
        /* @var $template OnlineCastingForm */
        $template     = OnlineCastingForm::getCastingInfo();
        $roleTemplate = OnlineCastingForm::getRoleInfo();
        
        // создаем заготовки для проектов, мероприятий и ролей
        $project = new Project();
        $event   = new ProjectEvent();
        $vacancy = new EventVacancy();
        
        // сохраняем информацию о проекте
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
        
        // @todo создаем заказ, который отправит сообщение команде
        
        // удаляем проект онлайн-кастинга из сессии
        Yii::app()->session->remove('onlineCasting');
        
        return true;
    }
    
    /**
     * 
     * @return void
     * 
     * @todo нужно создавать заказ, который уже сам создаст задачу в Мегаплане, а не создавать ее здесь
     */
    protected function addMegaplanTask()
    {
        $description = $this->createDescription();
    
        // создаем данные для задачи
        $task = array();
        $task['Model[Name]']        = 'Новый запрос онлайн-кастинга '.date('Y-m-d H:i');
        $task['Model[Responsible]'] = '1000004';
        $task['Model[Statement]']   = $description;
    
        // создаем задачу в Мегаплане
        $result = Yii::app()->megaplan->createTask($task);
    }
    
    /**
     * Если у заказчика вдруг что-то случилось, то обязательно просим его связаться с оператором,
     * чтобы не потерять заказ.
     * @return void
     */
    protected function getCustomerErrorMessage()
    {
        return 'Отчет отправлен в нашу техническую службу, пожалуйста обратитесь к нашему оператору
            (онлайн-помощь внизу экрана), 
            чтобы мы все равно смогли создать для вас кастинг несмотря ни на что :)';
    }
}