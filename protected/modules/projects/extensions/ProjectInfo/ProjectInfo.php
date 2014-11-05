<?php

/**
 * Виджет для отображения информации о проекте
 * Также отображает информацию по мероприятиям и вакансиям
 * 
 * @todo дать возможность участникам отменять заявки
 * @todo вынести весь JS во внешние файлы
 * @todo добавить отображение фото и видео для мероприятия
 * @todo убрать отсюда функции генерации кнопок и заменить их вызовом виджета VacancyActions
 */
class ProjectInfo extends CWidget
{
    /**
     * @var int - id отображаемого проекта.
     *            Не указывается, если отображается мероприятие или вакансия
     */
    public $projectId;
    /**
     * @var int - id отображаемого мероприятия
     *            Не указывается, если отображается проект или вакансия
     */
    public $eventId;
    /**
     * @var int - id отображаемой вакансии
     */
    public $vacancyId;
    /**
     * @var array - список тех вкладок, которые нужно отобразить (по умолчанию - все)
     */
    public $displayTabs = array();
    /**
     * @var array - список блоков с информацией,которые нужно отобразить (по умолчанию - все) 
     *              Возможные значения: 'title', 'info', 'data'
     */
    public $displaySections = array();
    /**
     * @var array - Какую информацию отображать в разделе "краткое описание" (project, event)
     */
    public $displayInfo = array('project');
    /**
     * @var string - активная при загрузке виджета вкладка информации о проекте (если отображается проект)
     *               Возможные значения: 'main', 'photo', 'video', 'events', 'vacancies'
     */
    public $activeTab = 'main';
    /**
     * @var bool - делать ли заголовок виджета ссылкой на отображаемый объект?
     */
    public $linkTitle = false;
    
    /**
     * @var Project
     */
    protected $project;
    /**
     * @var ProjectEvent
     */
    protected $event;
    /**
     * @var EventVacancy
     */
    protected $vacancy;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        Yii::import('projects.models.*');
        
        // Определяем, какие разделы и вкладки по умолчанию отобразить
        if ( ! $this->displaySections )
        {
            $this->displaySections = $this->getDefaultSections();
        }
        if ( ! $this->displayTabs )
        {
            $this->displayTabs = $this->getDefaultTabs();
        }
        
        // Определим, что отображать: мероприятие, проект или конкретную вакансию
        if ( $this->vacancyId AND $vacancy = EventVacancy::model()->findByPk($this->vacancyId) )
        {// отображается вакансия
            $this->eventId   = $vacancy->event->id;
            $this->projectId = $vacancy->event->project->id;
            $this->project   = $vacancy->event->project;
            $this->event     = $vacancy->event;
            $this->vacancy   = $vacancy;
            return;
        }
        if ( $this->eventId AND $event = ProjectEvent::model()->findByPk($this->eventId) )
        {// отображается мероприятие
            $this->projectId = $event->project->id;
            $this->project   = $event->project;
            $this->event     = $event;
            return;
        }
        if ( ! $this->projectId OR ! $project= Project::model()->findByPk($this->projectId) )
        {// Нужно отобразить проект, но его id не указан - это ошибка
            throw new CException(500, 'Не указан id проекта, мероприятия или вакансии для отображения');
        }
        $this->project = $project;
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        // отображаем заголовок
        $title = $this->displayTitleSection();
        // Отображаем краткую информацию
        $info = $this->displayInfoSection();
        // отображаем полную информацию
        $data = $this->displayDataSection();
        
        $this->render('project', array(
            'title' => $title,
            'info'  => $info,
            'data'  => $data,
        ));
    }
    
    /**
     * Получить список вкладок с информацией (о проекте или мероприятии), которые нужно отобразить
     * (по умолчанию отображаются все, кроме тех которые запрещены правами доступа)
     *
     * @return array
     */
    protected function getDefaultTabs()
    {
        if ( $this->eventId )
        {// отображается мероприятие
            return array('main', /*'photo',*/ 'video', 'vacancies', 'requests');
        }
        // отображается проект
        return array('main', 'photo', 'video', 'events', 'vacancies', 'requests');
    }
    
    /**
     * Получить список разделов информации, которые нужно отобразить по умолчанию
     * 
     * @return array
     */
    protected function getDefaultSections()
    {
        return array('title', 'projectinfo', 'data');
    }
    
    /**
     * Отобразить заголовок наверху виджета (название проекта или название мероприятия)
     * 
     * @return string
     * 
     * @todo предусмотреть многодневные события
     */
    protected function displayTitleSection()
    {
        $title = '';
        $type  = '';
        
        if ( $this->eventId )
        {// отображается мероприятие
            $title  = CHtml::encode($this->event->name);
            $type   = $this->event->getFormattedTimePeriod();
            if ( $this->linkTitle )
            {// показываем название как ссылку
                $title = CHtml::link($title, $this->getEventUrl());
            }
            if ( $this->event->type != 'event' )
            {// если для события задан тип - покажем его
                $type = $this->event->getTypeLabel().'<br>'.$type;
            }
        }else
        {// отображается проект
           $title = CHtml::encode($this->project->name);
           if ( $this->linkTitle )
           {// показываем название как ссылку
               $title = CHtml::link($title, $this->getProjectUrl());
           }
           $type = $this->project->getTypeLabel();
        }
        
        return $this->render('_title', array(
            'title' => $title,
            'type'  => $type,
        ), true);
    }
    
    /**
     * Отобразить краткую информацию о проекте или мероприятии
     * 
     * @return string
     */
    protected function displayInfoSection()
    {
        $result = '';
        // кнопка "настройки" для проекта или мероприятия  - ее видят только администраторы
        // сделана для удобства перехода в админку и обратно
        $adminButton = $this->createAdminButton();
        
        if ( in_array('project', $this->displayInfo) )
        {// нужно отобразить информацию о проекте
            $imageUrl    = $this->project->getAvatarUrl('full');
            $image       = CHtml::image($imageUrl, CHtml::encode($this->project->name), array('style' => 'max-width:100%;'));
            $image       = '<p class="text-center">'.CHtml::link($image, $this->getProjectUrl()).'</p>';
            $description = $this->project->shortdescription;
            
            $result .= $this->render('_info', array(
                'image'       => $image,
                'description' => $description,
                'adminButton' => $adminButton,
            ), true);
        }
        if ( $this->eventId AND in_array('event', $this->displayInfo) )
        {// Нужно отобразить информацию о событии
            $image       = '';
            $description = $this->event->description;
            
            $result .= '<hr>';
            $result .= $this->render('_info', array(
                'image'       => $image,
                'description' => $description,
                'adminButton' => $adminButton,
            ), true);
        }
        
        return $result;
    }
    
    /**
     * Отобразить данные по проекту или мероприятию (в зависимости от того что отображаем)
     * 
     * @return null
     */
    protected function displayDataSection()
    {
        if ( $this->eventId )
        {// выводим информацию о мероприятии
            $tabs = $this->getEventTabs();
        }else
        {// выводим информацию о проекте
            $tabs = $this->getProjectTabs();
        }
        // Выводим сам виджет с вкладками
        return $this->widget('bootstrap.widgets.TbTabs', array(
            'type'      => 'tabs',
            'placement' => 'right',
            'tabs'      => $tabs,
        ), true);
    }
    
    /**
     * Получить все вкладки с информацией о проекте
     * 
     * @return array
     */
    protected function getProjectTabs()
    {
        $tabs = array();
        
        foreach ( $this->displayTabs as $tabName )
        {
            if ( $tab = $this->getProjectTab($tabName) )
            {
                $tabs[] = $tab;
            }
        }
        
        return $tabs;
    }
    
    /**
     * Получить вкладку с информацией о проекте (используется при просмотре проекта)
     *
     * @param string $name - короткое название отображаемой вкладки
     * @return array|false - вкладка вместе содержимым или false если вкладку отображать не нужно
     */
    protected function getProjectTab($name)
    {
        $content = '';
        $active  = false;
        
        if ( ! in_array($name, $this->displayTabs) )
        {// вкладку с информацией не нужно отображать
            return false;
        }
        switch ( $name )
        {// определяем, из какой вкладки отображать информацию
            case 'main':      $content = $this->getProjectMainTab(); break;
            case 'photo':     $content = $this->getProjectPhotoTab(); break;
            case 'video':     $content = $this->getProjectVideoTab(); break;
            case 'events':    $content = $this->getProjectEventsTab(); break;
            case 'vacancies': $content = $this->getProjectVacanciesTab(); break;
            case 'requests':  $content = $this->getProjectRequestsTab(); break;
        }
        if ( ! $content )
        {// во вкладке нет никакой информации - значит отображать ее не нужно
            return false;
        }
        if ( $name === $this->activeTab )
        {// делаем вкладку активной если нужно
            $active = true;
        }
        
        // соберем массив для создания вкладки в элементе TbTabs
        return array(
            'content' => $content,
            'label'   => ProjectsModule::t('projectinfo_section_'.$name),
            'active'  => $active,
        );
    }
    
    /**
     * Получить содержимое вкладки "описание"
     * (информация о проекте)
     * 
     * @return string
     * 
     * @todo не показывать описание если его нет, и показывать хоть какое-то описание если нет нужного
     */
    protected function getProjectMainTab()
    {
        $content = '';
        if ( $this->customerView() )
        {// заказчикам показываем описание для заказчика
            $content .= '<p>'.$this->getProjectDescription($this->project).'</p>';
        }elseif ( $this->userView() )
        {// всем остальным - описание для участника
            $content .= '<p>'.$this->getProjectDescription($this->project).'</p>';
        }elseif ( $this->adminView() )
        {// Админу показываем и то и другое
            $content .= '<p>(Для заказчика):<br>'.$this->project->customerdescription.'</p>';
            $content .= '<p>(Для участника):<br>'.$this->project->description.'</p>';
        }
        
        return $content;
    }
    
    /**
     * Получить полное описание проекта (для участника или заказчика)
     * Если нет нужного - подставляет хотя бы какое-нибудь
     * @param Project $project
     * @return string
     */
    protected function getProjectDescription($project)
    {
        if ( ! $result = $this->project->description )
        {
            $result = $this->project->customerdescription;
        }
        return $result;
    }
    
    /**
     * Получить содержимое вкладки "фото"
     * (информация о проекте)
     *
     * @return string
     */
    protected function getProjectPhotoTab()
    {
        if ( ! $photos = $this->project->photoGalleryBehavior->getGalleryPhotos() )
        {// у проекта нет фотографий
            return false;
        }
        
        $content  = '<h3>'.ProjectsModule::t('projectinfo_section_photo').'</h3>'; 
        $content .= $this->widget('ext.ECMarkup.EThumbCarousel.EThumbCarousel', array(
            'previews'    => $this->project->getBootstrapPhotos('small'),
            'photos'      => $this->project->getBootstrapPhotos('medium'),
            'largePhotos' => $this->project->getBootstrapPhotos('full'),
        ), true);
        
        return $content;
    }
    
    /**
     * Получить содержимое вкладки "видео"
     * (для проекта)
     *
     * @return string
     */
    protected function getProjectVideoTab()
    {
        return $this->widget('ext.ECMarkup.ECVideoList.ECVideoList', array(
            'objectType' => 'project',
            'objectId'   => $this->project->id,
        ), true);
    }
    
    /**
     * Получить содержимое вкладки "мероприятия"
     * (информация о проекте)
     *
     * @return string
     * 
     * @todo помечать прошедшие события другим цветом
     * @todo выводить для админов запланирование мероприятия
     * @todo размещать наверху мероприятия на которые записан участник
     * @todo добавлять бейджик "вы участвуете"/"вы участвовали"
     * @todo выводить бейджик "идет сейчас"
     * @todo вынести весь список бейджей в класс мероприятия (как в анкетах) и сделать отдельный виджет для их вывода
     * @todo вынести отображение одного мероприятия в отдельную функцию
     * @todo добавить в список бейджей "начнется через..."
     */
    protected function getProjectEventsTab()
    {
        $content = '';
        
        if ( ! $events = $this->project->userevents )
        {// нет доступных пользователю мероприятий
            return false;
        }
        foreach ( $this->project->activeevents as $event )
        {// выводим активные мероприятия
            $bages        = '';
            $signInButton = $this->createSignInButton($event);
            
            $content .= $this->render('_event', array(
                'event'        => $event,
                'bages'        => $bages,
                'signInButton' => $signInButton,
            ), true);
        }
        
        foreach ( $this->project->finishedevents as $event )
        {// выводим завершенные мероприятия
            $bages = '';
            $signInButton = $this->createSignInButton($event);
            if ( $event->expired )
            {// событие уже прошло
                $bages .= $this->widget('bootstrap.widgets.TbBadge', array(
                    'type'  => 'default',
                    'label' => ProjectsModule::t('expired_event'),
                ), true).' ';
            }
            $content .= $this->render('_event', array(
                'event'        => $event,
                'bages'        => $bages,
                'signInButton' => $signInButton,
            ), true);
        }
        
        if ( $content )
        {// выводим заголовок если есть хотя бы одно мероприятие
            $content = '<h3>'.ProjectsModule::t('projectinfo_section_events').'</h3>'.$content;
        }
        
        return $content;
    }
    
    /**
     * Получить содержимое вкладки "вакансии"
     * (информация о проекте)
     *
     * @return string
     * 
     * @todo показывать админам шкалу заполнения вакансии
     * @todo для админов выводить незаполненные вакансии первыми 
     * @todo языковые строки
     * @todo переписать права доступа
     */
    protected function getProjectVacanciesTab()
    {
        return $this->getVacanciesTab('project');
    }
    
    /**
     * Получить вкладку со списком заявок для всего проекта
     * (видна только админам)
     * @return string
     */
    protected function getProjectRequestsTab()
    {
        if ( ! $this->adminView() )
        {
            return;
        }
        return $this->widget('admin.extensions.ProjectMembers.ProjectMembers',array(
            'objectType'           => 'project',
            'objectId'             => $this->project->id,
            'displayType'          => 'applications',
            'displayTimeColumn'    => false,
            'displayVacancyColumn' => false,
            'displayHeader'        => false,
        ), true);
    }
    
    /**
     * Отобразить вкладку со списком вакансий для проекта или мероприятия
     * @param string $type - event/project
     * @return string
     */
    protected function getVacanciesTab($type)
    {
        $content = '<h3>'.ProjectsModule::t('projectinfo_section_vacancies').'</h3>';
        
        if ( $this->customerView() )
        {// заказчикам раздел вакансий не показывается
            return '';
        }
        
        if ( $type == 'project' )
        {// все вакансии проекта
            $vacancies = $this->project->getAvailableVacancies();
        }else
        {// все вакансии мероприятия
            $vacancies  = $this->event->activevacancies;
        }
        
        if ( ! $vacancies )
        {// Участнику не подходит ни одна роль - сообщим об этом
            $content .= '<div class="alert alert-info">В этом проекте нет подходящих ролей</div>';
            return $content;
        }
        
        foreach ( $vacancies as $vacancy )
        {
            $content .= $this->displayVacancyInstance($vacancy);
        }
        
        return $content;
    }
    
    /**
     * Отобразить информацию по одной вакансии
     * @param EventVacancy $vacancy
     * @return string
     * 
     * @todo разрешить отзывать заявки, причем только не одобренные
     * @todo если вакансия создана для группы - то выводить специальное сообщение со списком мероприятий
     *       к которым она принадлежит
     */
    protected function displayVacancyInstance($vacancy)
    {
        if ( ! $vacancy->isAvailableForUser(null, true) AND ! $this->adminView() )
        {// участник не проходит по критериям вакансии - не покажем ее
            return '';
        }
        
        return $this->render('_vacancy', array(
            'vacancy' => $vacancy,
        ), true);
    }
    
    /**
     * Получить все вкладки мероприятия (каждая вкладка - информация о вакансии + Стандартные)
     * 
     * @return array
     * 
     * @todo добавить в меню разделитель "вакансии"
     * @todo добавить вкладку "Описание"
     * @todo добавить вкладку "Фото"
     * @todo добавить вкладку "Видео"
     * @todo добавить вкладку "Кто еще будет"(для участников)/"участники" (для гостей и заказчиков)
     * @todo добавить вкладку "Для участников" (только для подтвержденных участников)
     * @todo добавить вкладку "отзывы"
     * @todo разбить информацию о мероприятии на вертикальные и горизонтальные вкладки или сделать каждую вакансию
     *        отдельной вкадкой
     */
    protected function getEventTabs()
    {
        $tabs = array();
        
        foreach ( $this->displayTabs as $tabName )
        {
            if ( $tab = $this->getEventTab($tabName) )
            {
                $tabs[] = $tab;
            }
        }
        
        return $tabs;
    }
    
    /**
     * Получить вкладку с информацией о событии (используется при просмотре мероприятия)
     * 
     * @param string $name - короткое название отображаемой вкладки
     * @return array|bool - вкладка вместе содержимым или false если вкладку отображать не нужно
     */
    protected function getEventTab($name)
    {
        $content = '';
        
        if ( ! in_array($name, $this->displayTabs) )
        {// вкладку с информацией не нужно отображать
            return false;
        }
        
        switch ( $name )
        {
            case 'main':      $content = $this->getEventMainTab(); break;
            case 'vacancies': $content = $this->getEventVacanciesTab(); break;
            case 'requests':  $content = $this->getEventRequestsTab(); break;
            case 'video':     $content = $this->getEventVideoTab(); break;
        }
        
        if ( ! $content )
        {// во вкладке нет никакой информации - значит отображать ее не нужно
            return false;
        }
        
        // во вкладке есть информация - соберем из нее нужный массив
        $tab = array();
        $tab['label']   = ProjectsModule::t('eventinfo_section_'.$name);
        $tab['content'] = $content;
        if ( $name == $this->activeTab )
        {// делаем вкладку активной если нужно
            $tab['active'] = true;
        }
        
        return $tab;
    }
    
    /**
     * Получить вкладку с описанием события
     *
     * @return string
     */
    protected function getEventMainTab()
    {
        return '<p>'.$this->event->description.'</p>';
    }
    
    /**
     * Получить вкладку со списком вакансий мероприятия
     *
     * @return string
     */
    protected function getEventVacanciesTab()
    {
        return $this->getVacanciesTab('event');
    }
    
    /**
     * Получить вкладку со списком заявок мероприятия
     * (видна только админам)
     * @return string
     */
    protected function getEventRequestsTab()
    {
        if ( ! $this->adminView() )
        {
            return;
        }
        return $this->widget('admin.extensions.ProjectMembers.ProjectMembers',array(
            'objectType'           => 'event',
            'objectId'             => $this->event->id,
            'displayType'          => 'applications',
            'displayTimeColumn'    => false,
            'displayVacancyColumn' => false,
            'displayHeader'        => false,
        ), true);
    }
    
    /**
     * Получить содержимое вкладки "видео"
     * (для мероприятия)
     *
     * @return string
     */
    protected function getEventVideoTab()
    {
        return $this->widget('ext.ECMarkup.ECVideoList.ECVideoList', array(
            'objectType' => 'event',
            'objectId'   => $this->event->id,
        ), true);
    }
    
    /**
     * Получить ссылку на просмотр проекта
     * @return string
     */
    protected function getProjectUrl()
    {
        return Yii::app()->createUrl('/projects/projects/view', array('id' => $this->project->id));
    }
    
    /**
     * Получить ссылку на мероприятие
     * @param string $eventId
     * @return string
     */
    protected function getEventUrl($eventId=null)
    {
        if ( ! $eventId )
        {
            $eventId = $this->event->id;
        }
        return Yii::app()->createUrl('/projects/projects/view', array('eventid' => $eventId));
    }
    
    /**
     * Возвращает true только если страницу просматривает заказчик (обычный или незарегистрированный)
     * @return boolean
     */
    protected function customerView()
    {
        if ( ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('Customer') ) AND ! $this->adminView() )
        {
            return true;
        }
        return false;
    }
    
    /**
     * Возвращает true только если страницу просматривает админ
     * @return bool
     */
    protected function adminView()
    {
        return Yii::app()->user->checkAccess('Admin');
    }
    
    /**
     * Возвращает true только если страницу просматривает участник
     * @return boolean
     */
    protected function userView()
    {
        if ( Yii::app()->user->checkAccess('User') AND ! $this->adminView() )
        {
            return true;
        }
        return false;
    }
    
    /**
     * Создать кнопку для участия в мероприятии. Кнопка является ссылкой, перенаправляющей 
     * участника на страницу мероприятия
     * Кнопка по разному выглядит и называется, в зависимости от того, прошло событие или нет
     * 
     * @param int $eventId
     * @return string
     * 
     * @todo языковые строки
     * @todo показать гостю кнопку "участвовать"
     */
    protected function createSignInButton($event)
    {
        if ( $event->expired OR Yii::app()->user->isGuest )
        {
            $text  = 'Подробнее';
            $class = 'btn btn-primary';
        }else
        {
            $text  = 'Участвовать';
            $class = 'btn btn-success';
        }
        return CHtml::link($text, $this->getEventUrl($event->id), array('class' => $class));
    }
    
    /**
     * Отобразить кнопкцу "настройки" для администрирования проекта или мероприятия
     * @return string
     */
    protected function createAdminButton()
    {
        if ( ! $this->adminView() )
        {
            return '';
        }
        
        if ( $this->eventId )
        {// отображается мероприятие
            $url = Yii::app()->createUrl('/admin/projectEvent/view', array('id' => $this->eventId));
        }else
        {// отображается проект
            $url = Yii::app()->createUrl('/admin/project/view', array('id' => $this->projectId));
        }
        return $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'link',
            'type'       => 'warning',
            'size'       => 'large',
            'label'      => 'Настройки',
            'url'        => $url,
            'icon'       => 'icon-gear white',
        ), true);
    }
}