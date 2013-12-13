<?php

/**
 * Форма создания онлайн-кастинга
 * При сохранении создает проект и событие
 */
class OnlineCastingForm extends CFormModel
{
    public $projectname;
    public $projecttype;
    public $plandate;
    public $projectdescription;
    public $eventdescription;
    public $name;
    public $lastname;
    public $email;
    public $phone;
    
    /**
     * @see CFormModel::init()
     */
    public function init()
    {
        Yii::import('ext.LPNValidator.LPNValidator');
        Yii::import('projects.models.*');
    }
    
    /**
     * @see CModel::rules()
     */
    public function rules()
    {
        return array(
            array('projectname, name, lastname, email, phone', 'filter', 'filter' => 'trim'),
            // Сохраняем номер телефона в правильном формате (10 цифр)
            array('phone', 'LPNValidator', 'defaultCountry' => 'RU', 'allowEmpty' => true),
            array('email', 'email'),
            array('plandate', 'safe'), //comment
            array('projectname, projecttype, projectdescription, eventdescription, name, email', 'required'),
        );
    }
    
    /**
     * @see CModel::attributeLabels()
     */
    public function attributeLabels()
    {
        return array(
            // информация о проекте и мероприятии
            'projectname' => 'Название проекта',
            'projecttype' => 'Тип проекта',
            'plandate'    => 'Когда планируется съемка?',
            'projectdescription' => 'Расскажите о проекте для которого планируется кастинг',
            'eventdescription'   => 'Расскажите о съемках. Что будет происходить, каковы задачи?',
            // контакты заказчика
            'name'        => 'Имя',
            'lastname'    => 'Фамилия',
            'email'       => 'email',
            'phone'       => 'Телефон',
        );
    }
    
    /**
     * Сохранить запрос на расчет стоимости
     * Добавляет в Мегаплан новую задачу со всей информацией
     * @return boolean
     */
    public function save()
    {
        if ( isset($_SESSION['onlineCasting']['info']) )
        {
            unset($_SESSION['onlineCasting']['info']);
        }
        $_SESSION['onlineCasting']['info'] = $this;
        
        return true;
    }
    
    /**
     * Окончательно сохранить данные онлайн-кастинга в базу
     * @return void
     * 
     * @todo определить кого назначать руководителем проекта по умолчанию
     */
    public function finalize()
    {
        if ( ! isset($_SESSION['onlineCasting']['info']) )
        {// в сессии нет данных об онлайн-кастинге - не можем сохранить данные в базу
            return false;
        }
        /* @var $template OnlineCastingForm */
        $template = $_SESSION['onlineCasting']['info'];
        
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
            throw new CException('Не удалось сохранить проект онлайн-кастинга');
        }
        
        $event->name = $template->projectname;
        if ( $template->plandate )
        {// у кастинга есть планируемая дата проведения
            $event->timestart = CDateTimeParser::parse($date, Yii::app()->params['inputDateFormat']);
        }else
        {// кастинг без определенной даты
            $event->nodates = true;
        }
        $event->description = $template->eventdescription;
        $event->virtual     = 1;
        $event->type        = ProjectEvent::TYPE_CASTING;
        if ( ! $event->save() )
        {
            throw new CException('Не удалось сохранить съемочный день для онлайн-кастинга');
        }
        
        return true;
    }
    
    /**
     * 
     * @return void
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
        //CVarDumper::dump($result, 10, true);die;
    }
    
    /**
     * Создать описание онлайн-кастинга для отправки задачи в Мегаплан
     * @return string
     */
    protected function createDescription()
    {
        $result = 'Новый запрос онлайн-кастинга<br>';
        
        return $result;
    }
}