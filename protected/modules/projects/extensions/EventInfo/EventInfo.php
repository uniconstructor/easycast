<?php

/**
 * Виджет, отображающий краткую информацию об одном мероприятии в виде блока
 * Может содержать список доступных участнику ролей, с кнопкой подачи заявки для каждой роли
 * Используется в слайдере событий на главной, на странице "наши события", в анкете пользователя
 * 
 * @todo решить проблему с всплывающей не в том месте подсказкой для проекта
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
    public $vacancyListMode  = 'popover';
    /**
     * @var string - расположение всплывающей подсказки со списком ролей
     */
    public $popoverPosition  = 'bottom';
    /**
     * @var string - разворачивать ли список доступных ролей изначально? (только если rolesMode='list')
     *               always   - всегда разворачивать
     *               requests - только если есть поданые заявки
     *               never    - никогда не разворачивать
     */
    public $expandRoles      = 'never';
    /**
     * @var bool - отображать ли таймер обратного отсчета рядом с событием?
     */
    public $displayTimer = false;
    /**
     * @var string - где располагать таймер?
     *               description - в описании мероприятия
     *               logo - вместо логотипа (только если задано "не отображать логотип")
     */
    public $timerPosition = 'description';
    /**
     * @var string - режим просмотра: заказчик (customer) или участник (user)
     */
    public $userMode;
    /**
     * @var string - префикс для составления уникальных id для html элементов внутри виджета
     */
    public $tagIdPrefix   = 'event_info';
    /**
     * @var bool - показывать троеточие в кратком описании мероприятия как ссылку на полное описание
     */
    public $dotsAsLink    = false;
    
    
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
        
        $cs->registerCssFile($this->_assetUrl.'/EventInfo.css');
        //$cs->registerScriptFile($this->_assetUrl.'/EventInfo.js', CClientScript::POS_END);
        $logoId     = $this->getElementId('logo', 'project');
        $initButtonScript = "$('#{$logoId}').click(function(){return false;});\n";
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
        if ( false )
        {// @todo доступных ролей нет - не добавляем скрипт для списка ролей
            return;
        }
        // Добавляем всплывающее окно с описанием проекта
        $projectInfoUrl = Yii::app()->createUrl('//projects/project/ajaxInfo');
        $this->widget('ext.ECMarkup.ECPopover.ECPopover', array(
            'triggerSelector'    => '#'.$this->getElementId('logo', 'project'),
            'html'               => true,
            'title'              => CJavaScript::encode($this->event->project->name),
            'placement'          => 'bottom',
            'htmlOptions'        => array('style' => 'width:400px;max-width:400px;'),
            'contentAjaxOptions' => array(
                'url'  => $projectInfoUrl,
                'data' => array(
                    'id' => $this->event->project->id,
                    Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken,
                ),
            'cache'      => false,
            'type'       => 'post',
        )));
        // добавляем на кнопку "участвовать" раскрывающийся список подходящих ролей
        
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
        $projectUrl   = Yii::app()->createUrl('/projects/projects/view', array(
            'id' => $project->id,
        ));
        
        // логотип проекта
        $image = CHtml::image($logoUrl, '', array(
            'class' => 'ec-event-info-logo img-polaroid media-object',
        ));
        // настройки логотипа: при клике на него должно открываться окно с полной информацией о проекте
        $imageLinkOptions = array(
            'id' => $this->getElementId('logo', 'project'),
        ); 
        $image = CHtml::link($image, $projectUrl, $imageLinkOptions);
        
        if ( $this->displayProjectName )
        {// отображаем название проекта под логотипом (если нужно)
            // параметры для ссылки на проект: всегда открывается в новом окне, добавляется подсказка
            $projectNameOptions  = array(
                'target'         => '_blank',
                'data-toggle'    => 'tooltip',
                'data-title'     => 'Перейти на страницу проекта<br>(в новом окне)',
                'data-html'      => true,
                'data-placement' => 'bottom',
            );
            $projectName = '<small class="muted">'.$this->event->project->name.'</small>';
            $projectName = Html::link($projectName, $projectUrl, $projectNameOptions);
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
    protected function getJoinButton()
    {
        $htmlOptions = array(
            'id'    => $this->getElementId('button', 'join'),
            'class' => 'pull-right',
        );
        $type  = 'success';
        $label = 'Участвовать';
        
        if ( Yii::app()->user->isGuest AND $this->userMode == 'user' )
        {// гость-участник просматривает страницу: надпись на кнопке "записаться", и вместо доступных ролей
            // она открывает modal-окно с регистрацией
            $htmlOptions['data-toggle'] = 'modal';
            $htmlOptions['data-target'] = '#registration-modal';
        }
        
        // FIXME убрать отладку
        if ( false OR Yii::app()->user->checkAccess('User') AND ! Yii::app()->user->checkAccess('Customer') )
        {// кнопку видит зарегистрированный пользователь
            if ( ! $this->getVacancyCount() )
            {// нет доступных ролей - сообщим об этом участнику и выключим кнопку
                $type  = 'default';
                $label = 'Нет подходящих ролей';
                $htmlOptions['disabled'] = 'disabled';
            }
        }
        return $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'  => 'link',
            'type'        => $type,
            'label'       => $label,
            'icon'        => 'star white',
            'url'         => '#',
            'htmlOptions' => $htmlOptions,
        ), true);
    }
    
    /**
     * Получить html-код кнопки "мои заявки", которая раскрывает список поданых заявок
     * 
     * @return string
     */
    protected function getRequestsButton()
    {
        $htmlOptions = array('id' => $this->getElementId('button', 'requests'));
        $type        = 'info';
        $label       = "Мои заявки";
        if ( $count = $this->getRequestCount() )
        {
            $label .= " ({$count})";
        }
        
        if ( Yii::app()->user->checkAccess('User') )
        {// кнопку видит зарегистрированный пользователь
            $htmlOptions['click'] = 'function(){alert("request");}';
        }
        return $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'  => 'link',
            'type'        => $type,
            'label'       => $label,
            'icon'        => 'tasks white',
            'url'         => '#',
            'htmlOptions' => $htmlOptions,
        ), true);
    }
    
    /**
     * Получить html-код кнопки "О проекте"
     * 
     * @return string
     */
    /*protected function getProjectInfoButton()
    {
        $htmlOptions = array('id' => $this->getElementId('button', 'projectinfo'));
        $type        = 'default';
        $label       = "О проекте";
        
        return $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'  => 'link',
            'type'        => $type,
            'label'       => $label,
            'icon'        => 'question-sign',
            'url'         => '#',
            'htmlOptions' => $htmlOptions,
        ), true);
    }*/
    
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
     * Отображать ли блок с кнопкой "участвовать" и информацией о доступных ролях?
     * @return bool
     */
    protected function displayVacancyInfo()
    {
        
    }
    
    /**
     * Отображать ли блок с кнопкой "мои заявки" и информацией о поданых заявках?
     * @return bool
     */
    /*protected function requestsBlockVisible()
    {
        if ( $this->userMode == 'customer' OR 
             Yii::app()->user->isGuest OR 
             Yii::app()->user->checkAccess('Admin') OR
             Yii::app()->user->checkAccess('Customer') )
        {// админам, заказчикам и гостям кнопка не показывается
            return false;
        }
        if ( ! $this->getRequestCount() )
        {// нет ни одной заявки - не показываем кнопку
            false;
        }
        return true;
    }*/
}