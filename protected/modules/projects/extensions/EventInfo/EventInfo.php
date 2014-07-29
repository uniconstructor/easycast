<?php

/**
 * Виджет, отображающий краткую информацию об одном мероприятии в виде блока
 * Может содержать список доступных участнику ролей, с кнопкой подачи заявки для каждой роли
 * Используется в слайдере событий на главной, на странице "наши события", в анкете пользователя
 * 
 * @todo решить проблему с всплывающей не в том месте подсказкой для проекта
 * @todo при наведении на кнопку "подать заявку" выводить подсказку "нажмите чтобы посмотреть список ролей"
 * @todo добавить количество возможных ролей для кнопки "участвовать" и для заявок
 * @todo для админов вместо "мои заявки" показывать "поданые заявки"
 * @todo выводить группы событий. При отображении времени писать все дни, в которые оно проходит.
 *       При подаче заявки на участие предупреждать пользователя, что он должен будет присутствовать все дни.
 */
class EventInfo extends CWidget
{
    /**
     * @var string - событие по которому скрываются все раскрытые popover-списки ролей
     */
    const HIDE_POPOVERS_EVENT = 'ecHideEventInfoPopovers';
    
    /**
     * @var int - id анкеты участника, для которого отображается список доступных ролей и поданых заявок
     */
    public $questionaryId       = 0;
    /**
     * @var bool - отображать ли логотип проекта?
     */
    public $displayLogo         = true;
    /**
     * @var bool - отображать ли название проекта под логотипом?
     *             (только если отображается лого проекта)
     */
    public $displayProjectName  = false;
    /**
     * @var bool - отображать ли список доступных ролей?
     */
    public $displayVacancies    = true;
    /**
     * @var bool - отображать ли поданные заявки?
     */
    public $displayRequests     = true;
    /**
     * @var bool - отображать ли кнопку "о проекте"
     */
    public $displayAboutProject = true;
    /**
     * @var string - как отобразить список ролей?
     *               popover - всплывающий блок
     *               list    - разворачивающийся div под информацией о событии
     */
    public $vacancyListMode     = 'popover';
    /**
     * @var string - расположение всплывающей подсказки со списком ролей
     */
    public $popoverPosition     = 'bottom';
    /**
     * @var string - разворачивать ли список доступных ролей изначально? (только если rolesMode='list')
     *               always   - всегда разворачивать
     *               requests - только если есть поданые заявки
     *               never    - никогда не разворачивать
     */
    public $expandRoles         = 'never';
    /**
     * @var bool - отображать ли таймер обратного отсчета рядом с событием?
     */
    public $displayTimer        = false;
    /**
     * @var string - где располагать таймер?
     *               description - в описании мероприятия
     *               logo - вместо логотипа (только если задано "не отображать логотип")
     */
    public $timerPosition       = 'description';
    /**
     * @var string - режим просмотра: заказчик (customer) или участник (user)
     */
    public $userMode;
    /**
     * @var string - префикс для составления уникальных id для html элементов внутри виджета
     */
    public $tagIdPrefix         = 'event_info';
    /**
     * @var bool - показывать троеточие в кратком описании мероприятия как ссылку на полное описание
     */
    public $dotsAsLink          = false;
    
    /**
     * @var ProjectEvent - отображаемое событие
     */
    protected $event;
    /**
     * @var Questionary - анкета, для которой определяется список доступных ролей и поданых заявок
     */
    protected $questionary;
    /**
     * @var EventInvite - приглашение на мероприятие:если передан, то перед тем как отобразить список ролей
     *                    участнику будет предложено сначала принять или отклонить приглашение
     */
    protected $invite;
    /**
     * @var string
     */
    protected $_assetUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        if ( ! in_array($this->userMode, array('user', 'customer')) )
        {
            $this->userMode = Yii::app()->getModule('user')->getViewMode();
        }
        if ( ! $this->questionary )
        {// нужно самостоятельно определить пользователя для списка ролей и заявок
            if ( Yii::app()->user->isGuest )
            {// гость
                $this->questionary   = null;
                $this->questionaryId = 0;
            }else
            {// обычный пользователь: загружаем его данные
                $this->questionary   = Yii::app()->getModule('user')->user()->questionary;
                $this->questionaryId = $this->questionary->id;
            }
        }
        $this->registerAssets();
        
        parent::init();
    }
    
    /**
     * Подключить все скрипты и стили
     * @return void
     */
    protected function registerAssets()
    {
        $cs = Yii::app()->clientScript;
        $this->_assetUrl = Yii::app()->assetManager->
            publish(Yii::getPathOfAlias('projects.extensions.EventInfo.assets').DIRECTORY_SEPARATOR);
        
        $logoId     = $this->getElementId('logo', 'project');
        $initButtonScript = "$('#{$logoId}').click(function(){return false;});\n";
        
        $cs->registerCssFile($this->_assetUrl.'/EventInfo.css');
        //$cs->registerScriptFile($this->_assetUrl.'/EventInfo.js', CClientScript::POS_END);
        $cs->registerScript('_initEventInfoButton#'.$this->event->id, $initButtonScript, CClientScript::POS_END);
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $logo         = $this->getProjectLogo();
        $contentClass = 'span12';
        $eventUrl     = Yii::app()->createUrl('/projects/projects/view', array(
            'eventid' => $this->event->id,
        ));
        
        if ( $this->displayLogo )
        {// если отображается лого проекта - то ширина блока с информацией о событии 9, а если не отображается то 12
            $contentClass = 'span9';
        }
        
        // выводим всю информацию по мероприятию
        $this->render('event', array(
            'event'          => $this->event,
            'eventUrl'       => $eventUrl,
            'eventLabels'    => $this->getEventLabels(),
            'logo'           => $logo,
            'shortEventInfo' => $this->getShortEventInfo(),
            'contentClass'   => $contentClass,
        ));
        // Добавляем всплывающее окно с описанием проекта
        $projectInfoUrl = Yii::app()->createUrl('//projects/project/ajaxInfo');
        $logoSelector   = '#'.$this->getElementId('logo', 'project');
        $this->widget('ext.ECMarkup.ECPopover.ECPopover', array(
            'triggerSelector'    => $logoSelector,
            'html'               => true,
            'title'              => 'О проекте',
            'placement'          => 'bottom',
            'htmlOptions'        => array('style' => 'width:400px;max-width:400px;'),
            'contentAjaxOptions' => array(
                'url'   => $projectInfoUrl,
                'cache' => false,
                'type'  => 'post',
                'data'  => array(
                    'id' => $this->event->project->id,
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
            ),
        ));
        if ( $this->userMode === 'user' AND $this->vacancyListMode === 'popover' )
        {// добавляем на кнопку "участвовать" раскрывающийся список подходящих ролей
            switch ( $this->vacancyListMode )
            {
                case 'popover': $this->displayPopover(); break;
                //case 'list': $this->displayPopover(); break;
            }
        }
    }
    
    /**
     * Сеттер для поля "мероприятие" - проверяет, что мероприятие нужного типа и сообщает об ошибке, если
     * событие отобразить нельзя
     * @param ProjectEvent $event
     * @return void
     */
    public function setEvent($event)
    {
        if ( $event instanceof ProjectEvent )
        {// событие нужного типа, все ОК
            $this->event = $event;
        }else
        {// попытка отобразить несуществующее событие
            throw new InvalidArgumentException('Не передано мероприятие для отображения');
        }
    }
    
    /**
     * Сеттер для приглашения: проверяет его тип если оно передано
     * @param EventInvite $invite
     * @return void
     */
    public function setInvite($invite=null)
    {
        if ( ! $invite )
        {
            return;
        }
        if ( $invite instanceof EventInvite )
        {
            $this->invite = $invite;
        }else
        {
            throw new InvalidArgumentException('Неверный тип приглашения');
        }
    }
    
    /**
     * Сеттер для id анкеты: проверяет, существует ли анкета (если id передан извне)
     * Находит и получает данные о переданной анкете
     *
     * @param number $questionaryId
     * @return void
     */
    public function setQuestionaryId($questionaryId=0)
    {
        if ( $questionaryId )
        {// id анкеты передан извне
            $this->questionaryId = $questionaryId;
            if ( ! $this->questionary = Questionary::model()->findByPk($questionaryId) )
            {// переданная анкета не найдена
                throw new InvalidArgumentException('Не найдена анкета с переданным id: '.$questionaryId);
            }
        }
    }
    
    /**
     * Получить уникальный id для html-элемента внутри виджета
     * @param string $element - элемент для которого создается id
     *                          button - кнопка
     *                          container - div, содержащий нужную информацию
     * @param string $type - тип элемента (например для кнопок: join, requests, projectinfo)
     * @return string - уникальный id для элемента на странице
     */
    protected function getElementId($element, $type)
    {
        return $this->tagIdPrefix.'_'.$type.'_'.$element.'_'.$this->event->id;
    }
    
    /**
     * Получить html-код фрагмента с логотипом и названием проекта
     * @return string
     */
    protected function getProjectLogo()
    {
        if ( ! $this->displayLogo )
        {
            return '';
        }
        $projectName  = '';
        $project      = $this->event->project;
        $logoUrl      = $this->event->project->getAvatarUrl('small', true);
        $projectUrl   = Yii::app()->createUrl('/projects/projects/view', array('id' => $project->id));
        
        // логотип проекта
        $image = CHtml::image($logoUrl, '', array(
            'class' => 'ec-event-info-logo img-polaroid media-object',
            'id'    => $this->getElementId('logo', 'project'),
        ));
        // настройки логотипа: при клике на него должно открываться окно с полной информацией о проекте
        //$imageLinkOptions = array(); 
        //$image = CHtml::link($image, $projectUrl, $imageLinkOptions);
        
        if ( $this->displayProjectName )
        {// отображаем название проекта под логотипом (если нужно)
            // параметры для ссылки на проект: всегда открывается в новом окне, добавляется подсказка
            $projectNameOptions  = array(
                'target'         => '_blank',
                'data-toggle'    => 'tooltip',
                'data-title'     => 'Перейти на страницу проекта',
                'data-html'      => true,
                'data-placement' => 'bottom',
            );
            $projectName = '<small class="muted">'.$project->name.'</small>';
            $projectName = CHtml::link($projectName, $projectUrl, $projectNameOptions);
        }
        
        return $this->render('_logo', array(
            'image'       => $image,
            'projectName' => $projectName,
        ), true);
    }
    
    /**
     * Получить краткую аннотацию из описания мероприятия: обрезает первые 256 символов описания 
     * (до последнего целого слова) и вставляет в конце троеточие
     * 
     * @return string
     */
    protected function getShortEventInfo()
    {
        $shortEventInfo = strip_tags($this->event->description);
        $shortEventInfo = wordwrap($shortEventInfo, 256, '<!-- | -->');
        $shortEventInfo = explode('<!-- | -->', $shortEventInfo);
        $shortEventInfo = $shortEventInfo[0];
        
        if ( $this->dotsAsLink )
        {// добавляем троеточие как ссылку
            $eventUrl = Yii::app()->createUrl('/projects/projects/view', array(
                'eventid' => $this->event->id,
            ));
            $eventLink = CHtml::link('...', $eventUrl, array(
                'target'      => '_blank',
                'data-toggle' => 'tooltip',
                'style'       => 'text-decoration:underline;',
                'data-title'  => 'Посмотреть всю информацию (в отдельной вкладке)',
            ));
            $shortEventInfo .= $eventLink;
        }else
        {
            $shortEventInfo .= '...';
        }
        
        return $shortEventInfo;
    }
    
    /**
     * Получить поясняющие надписи для проекта: 
     * - тип проекта
     * - (другие свойства пока не придуманы)
     * @return string
     */
    protected function getProjectLabels()
    {
        
    }
    
    /**
     * Получить поясняющие надписи для мероприятия:
     * - тип мероприятия
     * - онлайн-кастинг или нет
     * - завершено или нет
     * - есть доступные роли или нет
     * @return string
     */
    protected function getEventLabels()
    {
        return $this->widget('projects.extensions.EventBages.EventBages', array('event' => $this->event), true);
    }
    
    /**
     * Получить html-код кнопки "записаться", которая раскрывает список доступных ролей
     * Если отображается не мероприятие а приглашение на съемку - то кнопка будет называться "принять приглашение"
     * Если доступных ролей нет - отображается серая кнопка с надписью "нет доступных ролей"
     * 
     * @return string
     */
    protected function getTopJoinButton()
    {
        $htmlOptions = array(
            'id'       => $this->getElementId('button', 'join'),
            'class'    => 'pull-right',
            'onclick'  => 'return false;',
        );
        $type  = 'success';
        $label = 'Участвовать';
        $icon  = 'star white';
        
        if ( Yii::app()->user->isGuest AND $this->userMode == 'user' )
        {// гость-участник просматривает страницу: надпись на кнопке "записаться", и вместо доступных ролей
            // она открывает modal-окно с регистрацией
            $label = 'Регистрация';
            $type  = 'primary';
            $icon  = '';
            $htmlOptions['data-toggle'] = 'modal';
            $htmlOptions['data-target'] = '#registration-modal';
        }
        
        if ( Yii::app()->user->checkAccess('User') AND ! Yii::app()->user->checkAccess('Customer') )
        {// кнопку видит зарегистрированный пользователь
            if ( ! $this->getVacancyCount() )
            {// нет доступных ролей - сообщим об этом участнику и выключим кнопку
                $type  = 'default';
                $label = 'Нет ролей';
                $icon  = 'remove';
                $htmlOptions['disabled'] = 'disabled';
            }
        }
        
        return $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'  => 'link',
            'type'        => $type,
            'label'       => $label,
            'icon'        => $icon,
            'url'         => '#',
            'htmlOptions' => $htmlOptions,
        ), true);
    }
    
    /**
     * Получить количество поданых участником заявок 
     * @return int
     */
    protected function getRequestCount()
    {
        
    }
    
    /**
     * Получить количество доступных участнику ролей 
     * @return int
     */
    protected function getVacancyCount()
    {
        return $this->event->countVacanciesFor($this->questionaryId);
    }
    
    /**
     * Проверить, есть ли у участника хотя бы одна подтвержденная заявка на это мероприятие
     * @return bool
     */
    protected function hasConfirmedRequest()
    {
        return $this->event->hasMember($this->questionaryId);
    }
    
    /**
     * Получить список ролей под событием
     * @return bool
     */
    protected function getVacancyList()
    {
        if ( ! $this->displayVacancies OR $this->vacancyListMode != 'list' )
        {// список внизу не отображается: вместо него сверху кнопка "участвовать"
            return '';
        }
        return $this->widget('projects.extensions.VacancyList.VacancyList', array(
            'objectType'  => 'event',
            'event'       => $this->event,
            'questionary' => $this->questionary,
        ), true);
    }
    
    /**
     * Отобразить popover-подсказку для элемента
     * @param string $url
     * @param string $selector
     * @return void
     */
    protected function displayPopover($url=null, $selector=null)
    {
        if ( ! $url )
        {
            $url = Yii::app()->createUrl('//projects/event/ajaxVacancyList');
        }
        if ( ! $selector )
        {
            $selector = '#'.$this->getElementId('button', 'join');
        }
        $this->widget('ext.ECMarkup.ECPopover.ECPopover', array(
            'triggerSelector'    => $selector,
            'html'               => true,
            'title'              => 'Роли в этот день',
            'placement'          => 'bottom',
            'htmlOptions'        => array('style' => 'width:400px;max-width:400px;'),
            'contentAjaxOptions' => array(
                'cache' => false,
                'url'   => $url,
                'type'  => 'post',
                'data'  => array(
                    'id'  => $this->event->id,
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
            ),
        ));
    }
}