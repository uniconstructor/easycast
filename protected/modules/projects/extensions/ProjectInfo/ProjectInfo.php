<?php

/**
 * Виджет для отображения информации о проекте
 * Также отображает информацию по мероприятиям и вакансиям
 * 
 * @todo переписать проверку прав
 * @todo дать возможность участникам отменять заявки
 */
class ProjectInfo extends CWidget
{
    /**
     * @var int - id отображаемого проекта.
     *             Не указывается, если отображается мероприятие или вакансия
     */
    public $projectId;
    
    /**
     * @var int - id отображаемого мероприятия
     *             Не указывается, если отображается проект или вакансия
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
     *               Возможные значения: 'title', 'info', 'data'
     */
    public $displaySections = array();
    
    /**
     * @var array - Какую информацию отображать в разделе "краткое описание" (project, event)
     */
    public $displayInfo = array('project');
    
    /**
     * @var string - активная при загрузке виджета вкладка информации о проекте (если отображается проект)
     * Возможные значения: 'main', 'photo', 'video', 'events', 'vacancies'
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
     * (non-PHPdoc)
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
            throw new CHttpException(500, 'Не указан id проекта, мероприятия или вакансии для отображения');
        }
        $this->project = $project;
    }
    
    /**
     * (non-PHPdoc)
     * @see CWidget::run()
     */
    public function run()
    {
        echo '<div class="row span12">';
        // отображаем заголовок
        $this->displayTitleSection();
        // Отображаем краткую информацию
        $this->displayInfoSection();
        // отображаем полную информацию
        $this->displayDataSection();
        echo '</div>';
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
            return array('main', /*'photo', 'video',*/ 'vacancies');
        }
        return array('main', 'photo', 'video', 'events', 'vacancies');
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
     * отобразить заголовок наверху виджета (название проекта или название мероприятия)
     * 
     * @return null
     * 
     * @todo предусмотреть многодневные события
     */
    protected function displayTitleSection()
    {
        $title = '';
        
        if ( $this->eventId )
        {// отображается мероприятие
            $title = CHtml::encode($this->event->name);
            $title .= ' '.date('Y/m/d', $this->event->timestart);
            $title .= ' '.date('H:i', $this->event->timestart);
            $title .= ' - '.date('H:i', $this->event->timeend);
            if ( $this->linkTitle )
            {// показываем название как ссылку
                $title = CHtml::link($title, $this->getEventUrl());
            }
        }else
       {// отображается проект
           $title = CHtml::encode($this->project->name);
           if ( $this->linkTitle )
           {// показываем название как ссылку
               $title = CHtml::link($title, $this->getProjectUrl());
           }
        }
        
        $title = '<h1>'.$title.'</h1>';
        
        echo $title;
    }
    
    /**
     * Отобразить краткую информацию о проекте или мероприятии
     * 
     * @return null
     */
    protected function displayInfoSection()
    {
        if ( in_array('project', $this->displayInfo) )
        {// нужно отобразить информацию о проекте
            $imageUrl = $this->project->getAvatarUrl('full');
            $image = CHtml::image($imageUrl, CHtml::encode($this->project->name));
            $image = '<p class="text-center">'.CHtml::link($image, $this->getProjectUrl()).'</p>';
            $description = $this->project->shortdescription;
            
            $this->render('_info', array(
                'image'       => $image,
                'description' => $description,
            ));
        }
        if ( $this->eventId AND in_array('event', $this->displayInfo) )
        {// Нужно отобразить информацию о событии
            $image = '';
            $description = $this->event->description;
            echo '<hr>';
            
            $this->render('_info', array(
                'image'       => $image,
                'description' => $description,
            ));
        }
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
        echo '<div class="span8">';
        // Выводим сам виджет с вкладками
        $this->widget('bootstrap.widgets.TbTabs', array(
            'type'      => 'tabs',
            'placement' => 'right',
            'tabs'      => $tabs,
        ));
        echo '</div>';
    }
    
    /**
     * Получить все вкладки с информацией о проектк
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
     * @return array|bool - вкладка вместе содержимым или false если вкладку отображать не нужно
     */
    protected function getProjectTab($name)
    {
        $content = '';
        
        if ( ! in_array($name, $this->displayTabs) )
        {// вкладку с информацией не нужно отображать
            return false;
        }
    
        switch ( $name )
        {
            case 'main':      $content = $this->getProjectMainTab(); break;
            case 'photo':     $content = $this->getProjectPhotoTab(); break;
            case 'video':     $content = $this->getProjectVideoTab(); break;
            case 'events':    $content = $this->getProjectEventsTab(); break;
            case 'vacancies': $content = $this->getProjectVacanciesTab(); break;
        }
        
        if ( ! $content )
        {// во вкладке нет никакой информации - значит отображать ее не нужно
            return false;
        }
        
        // во вкладке есть информация - соберем из нее нужный массив
        $tab = array();
        $tab['label']   = ProjectsModule::t('projectinfo_section_'.$name);
        $tab['content'] = $content;
        if ( $name == $this->activeTab )
        {// делаем вкладку активной если нужно
            $tab['active'] = true;
        }
        
        return $tab;
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
        $content = '<h3>'.ProjectsModule::t('projectinfo_section_main').'</h3>';
        if ( $this->customerView() )
        {// заказчикам показываем описание для заказчика
            $content .= '<p>'.$this->project->customerdescription.'</p>';
        }elseif ( $this->userView() )
        {// всем остальным - описание для участника
            $content .= '<p>'.$this->project->description.'</p>';
        }elseif ( $this->adminView() )
        {// Админу показываем и то и другое
            $content .= '<p>(Для заказчика):<br>'.$this->project->customerdescription.'</p>';
            $content .= '<p>(Для участника):<br>'.$this->project->description.'</p>';
        }
        
        return $content;
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
        
        $content = '<h3>'.ProjectsModule::t('projectinfo_section_photo').'</h3>'; 
        
        $content .= $this->widget('ext.ECMarkup.EThumbCarousel.EThumbCarousel', array(
            'previews'    => $this->project->getBootstrapPhotos('small'),
            'photos'      => $this->project->getBootstrapPhotos('medium'),
            'largePhotos' => $this->project->getBootstrapPhotos('full'),
        ), true);
        
        return $content;
    }
    
    /**
     * Получить содержимое вкладки "видео"
     * (информация о проекте)
     *
     * @return string
     */
    protected function getProjectVideoTab()
    {
        $content = '';
        
        return $content;
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
            $bages = '';
            $signInButton = $this->createSignInButton($event);
            
            $content .= $this->render('_event', array(
                'event' => $event,
                'bages' => $bages,
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
                'event' => $event,
                'bages' => $bages,
                'signInButton' => $signInButton,
            ), true);
        }
        
        if ( $content )
        {
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
        {
            $vacancies = $this->project->getAvailableVacancies();
        }else
       {
           $vacancies = $this->event->activevacancies;
        }
        
        if ( ! $vacancies )
        {// Участнику не подходит ни одна вакансия - сообщим об этом
            $content .= '<div class="alert alert-info">В этом проекте для вас нет подходящих вакансий</div>';
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
     * @todo выводить сообщение если заявка уже подана
     */
    protected function displayVacancyInstance($vacancy)
    {
        $addAppllicationButton    = '';
        $removeAppllicationButton = '';
        $messageClass = '';
        $messageText  = '';
        
         
        if ( ! $vacancy->hasApplication() )
        {// у участника уже есть заявка на эту вакансию
            $removeAppllicationButton = $this->createRemoveApplicationButton($vacancy->id);
        }else
       {// заявки на участие нет
            if ( $vacancy->isAvailableForUser() )
            {// если участник проходит по указанным в вакансии критериям - покажем ему кнопку подачи заявки
                $addAppllicationButton    = $this->createAddAppllicationButton($vacancy->id);
            }else
          {// а если не подходит - то даже вакансию ему не покажем
                return '';
            }
        }
        
        return $this->render('_vacancy', array(
            'vacancy' => $vacancy,
            'messageClass' => $messageClass,
            'messageText'  => $messageText,
            'addAppllicationButton'    => $addAppllicationButton,
            'removeAppllicationButton' => $removeAppllicationButton,
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
        $content = '<h3>'.ProjectsModule::t('projectinfo_section_main').'</h3>';
        $content .= '<p>'.$this->event->description.'</p>';
                
        return $content;
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
    
    protected function customerView()
    {
        if ( ( Yii::app()->user->isGuest OR Yii::app()->user->checkAccess('Customer') ) AND ! $this->adminView() )
        {
            return true;
        }
        return false;
    }
    
    protected function adminView()
    {
        return Yii::app()->user->checkAccess('Admin');
    }
    
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
     * Создать AJAX-кнопку для отправки заявки на участие в вакансии
     * @param int $vacancyId - id вакансии
     * @return string
     * 
     * @todo языковые строки
     * @todo обработать возможные ошибки AJAX
     * @todo добавить js-подтверждение при подаче заявки
     */
    protected function createAddAppllicationButton($vacancyId)
    {
        // Создаем параметры для кнопки
        $url = Yii::app()->createUrl('/projects/event/addApplication', array('vacancyid' => $vacancyId));
        $ajaxOptions = $this->createButtonAjaxOptions('add', $vacancyId);
        $htmlOptions = array(
            'class' => 'btn btn-success',
            'id' => 'add_application_'.$vacancyId);
        
        return CHtml::ajaxButton('Отправить заявку', $url, $ajaxOptions, $htmlOptions);
    }
    
    /**
     * Создать AJAX-кнопку для отмены заявки на участие в вакансии
     * @param int $vacancyId - id вакансии
     * @return string
     *
     * @todo языковые строки
     * @todo обработать возможные ошибки AJAX
     * @todo добавить js-подтверждение при отмене заявки
     */
    protected function createRemoveApplicationButton($vacancyId)
    {
        return '';
        // Создаем параметры для кнопки
        $url = Yii::app()->createUrl('/projects/event/removeApplication', array('vacancyid' => $vacancyId));
        $ajaxOptions = $this->createButtonAjaxOptions('remove', $vacancyId);
        $htmlOptions = array(
            'class' => 'btn btn-success',
            'id' => 'remove_application_'.$id);
        
        return CHtml::ajaxButton('Отозвать заявку', $url, $ajaxOptions, $htmlOptions);
    }
    
    /**
     * Создать настройки для кнопки с AJAX-запросом добавления или отзыва заявки на вакансию
     * @param string $type - для какой кнопки получить настройки (add, remove)
     * @param ind $vacancyId - id вакансии на которую подает заявку участник
     * @return array
     * 
     * @todo настроить beforeSend
     */
    protected function createButtonAjaxOptions($type, $vacancyId)
    {
        $ajaxOptions = array(
            'url'  => $this->createButtonAjaxUrl($type, $vacancyId),
            'data' => array(
                'vacancyid'  => $vacancyId,
                Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
            ),
            'type'    => 'post',
            'success'    => $successJS,
            //'error' =>
            //'beforeSend' => $beforeSendJS,
        );
        
        return $ajaxOptions;
    }
    
    protected function createAddApplicationSuccessJs($vacancyId)
    {
        $buttonId = 'add_application_'.$vacancyId;
        $messageId = 'vacancy_message_'.$vacancyId;
        $messageText = ProjectsModule::t('application_added');
        return "function (data, status){
            $(#'{$buttonId}').attr('class', 'btn disabled');
            $(#'{$buttonId}').attr('disabled', 'disabled');
            
            $(#'{$messageId}').attr('class', 'alert alert-success');
            $(#'{$messageId}').text('{$messageText}');
            $(#'{$messageId}').fadeIn(200);
        }";
    }
    
    protected function createRemoveApplicationSuccessJs($vacancyId)
    {
        $buttonId = 'add_application_'.$vacancyId;
        $messageId = 'vacancy_message_'.$vacancyId;
        $messageText = ProjectsModule::t('application_removed');
        return "function (data, status){
            $(#'{$buttonId}').hide();
        }";
    }
    
    protected function createButtonAjaxUrl($type, $vacancyId)
    {
        switch ( $type )
        {
            case 'add':    $action = 'addApplication'; break;
            case 'remove': $action = 'removeApplication'; break;
            default: return '#';
        }
        
        return Yii::app()->createUrl('/projects/event/'.$action, array('vacancyid' => $vacancyId));
    }
}