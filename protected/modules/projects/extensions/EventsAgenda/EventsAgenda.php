<?php

/**
 * Виджет для отображения списка событий, с возможностью принять участие в каждом из них
 * Имеет несколько режимов отображения: 
 * - слайдер, высотой в одну строку, с прокручивающимся списком (для главной страницы)
 * - полный список всех событий из всех проектов
 * - список всех предстоящих событий участника или проекта
 * 
 * @todo добавить режим отображения "все предстоящие события по объекту"
 * @todo добавить таймер с обратным отсчетом для каждого события 
 *       (с возможностью настройки: да / нет / только для n последних событий) 
 * @todo для админов добавить информацию по заполнению ролей
 * @todo писать название месяца для событий без конкретной даты "май-июнь" 2015
 */
class EventsAgenda extends CWidget
{
    /**
     * @var bool - отображать ли активные события?
     */
    public $displayActive   = true;
    /**
     * @var bool - отображать ли завершенные события?
     */
    public $displayFinished = false;
    /**
     * @var bool - показывать ли в общем списке событий онлайн-кастинги?
     */
    public $displayCastings = true;
    /**
     * @var int - сколько событий отображать максимально? (0 - все что есть)
     */
    public $eventLimit      = 10;
    /**
     * @var bool - отображать ли таймер обратного отсчета рядом с событием?
     */
    public $displayTimers   = false;
    /**
     * @var int - сколько последних событий будут с таймерами обратного отсчета?
     *            (0 - все события будут с таймерами)
     */
    public $timersCount     = 0;
    /**
     * @var string - режим отображения списка событий
     *               thumbnails - список блоков при помощи TbThumbnails
     *               timeline   - временная ось при помощи Timeline Blueprint
     */
    public $displayMode     = 'timeline';
    /**
     * @var CDbCriteria - дополнительные условия для поиска событий
     */
    public $criteria;
    /**
     * @var string - 
     */
    public $header = 'Наши события';
    /**
     * @var string - 
     */
    public $title;
    
    /**
     * @var string - режим просмотра: заказчик (customer) или участник (user)
     */
    protected $userMode;
    /**
     * @var CActiveDataProvider - источник данных для получения списка событий
     *                            (используется в режиме отображения thumbnails)
     */
    protected $dataProvider;
    /**
     * @var array - список событий (массив нужной структуры для виджета CdVerticalTimeLine)
     *              (используется в режиме отображения timeline)
     */
    protected $events;
    /**
     * @var Questionary|null - анкета участника, просматривающего страницу или null если это гость или заказчик
     */
    protected $questionary;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $this->userMode = Yii::app()->getModule('user')->getViewMode();
        // если не отображать ни активные ни завершенные события - то список всегда будет пустым
        if ( ! $this->displayActive AND ! $this->displayFinished )
        {
            throw new CException('Нужно выбрать хотя бы какие-то (активные или завершенные) события');
        }
        if ( ! in_array($this->displayMode, array('thumbnails', 'timeline')) )
        {
            throw new CException('Не выбран режим просмотра');
        }
        if ( $this->userMode === 'user' AND ! Yii::app()->user->isGuest )
        {
            $this->questionary = Yii::app()->getModule('user')->user()->questionary;
        }
        if ( ! $this->criteria )
        {
            $this->criteria = new CDbCriteria();
        }
        // получаем список последних событий с сайта
        $this->loadEvents();
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( ! $this->events )
        {// нет событий - отображать нечего
            return;
        }
        if ( $this->displayMode === 'thumbnails' )
        {
            $this->widget('bootstrap.widgets.TbThumbnails', array(
                'dataProvider' => $this->dataProvider,
                'itemView'     => '_thumbnailEvent',
                'emptyText'    => 'nodata',
                'template'     => '{items}',
            ));
        }elseif ( $this->displayMode === 'timeline' )
        {
            $this->render('agenda');
        }
    }
    
    /**
     * Получить список отображаемых событий
     * 
     * @return ProjectEvent
     */
    protected function loadEvents()
    {
        $statuses   = array();
        $pagination = false;
        $criteria   = new CDbCriteria();
        
        if ( $this->displayActive )
        {
            $statuses[] = ProjectEvent::STATUS_ACTIVE;
        }
        if ( $this->displayFinished )
        {
            $statuses[] = ProjectEvent::STATUS_FINISHED;
        }
        if ( $this->eventLimit > 0 )
        {
            $criteria->limit = $this->eventLimit;
        }
        if ( ! $this->displayCastings )
        {
            $criteria->compare('virtual', 0);
        }
        // отображаем только события с определенным статусом
        $criteria->addInCondition('status', $statuses);
        $criteria->scopes = array(
            'lastStarted',
        );
        // совмещаем условия выборки с дополнительными
        $this->criteria->mergeWith($criteria);
        
        if ( $this->displayMode === 'thumbnails' )
        {
            $this->dataProvider = new CActiveDataProvider('ProjectEvent', array(
                'criteria'   => $this->criteria,
                'pagination' => $pagination,
            ));
        }else
        {
            $this->events = $this->getTimeLineEvents($this->criteria);
        }
    }
    
    /**
     * Получить массив событий для использования в элементе CdVerticalTimeLine
     * 
     * @param CDbCriteria $criteria
     * @return array
     */
    protected function getTimeLineEvents($criteria)
    {
        $events = ProjectEvent::model()->findAll($criteria);
        $result = array();
        
        foreach ( $events as $event )
        {
            $result[] = $this->getTimeLineEvent($event);
        }
        return $result;
    }
    
    /**
     * Получить массив с данными одного события для использования в элементе CdVerticalTimeLine
     * 
     * @param  ProjectEvent $event
     * @return array
     * 
     * @todo для подтвержденных участников добавлять место сбора, доп. информацию и все остальное
     * @todo вынести стили в css-классы
     */
    protected function getTimeLineEvent($event)
    {
        // иконка события (если нужна)
        $iconImage = CHtml::image($event->project->getAvatarUrl('small'), '', array('style' => "height:75px;width:75px;margin-right:10px;float:left;"));
        // название события
        $name      = CHtml::link($iconImage, $event->url);
        $name     .= CHtml::link($event->project->name, $event->url, array('style' => 'font-weight:normal;color:#fff;text-transform:capitalize;'));
        
        if ( $event->nodates )
        {// мероприятие без конкретной даты - пишем "дата уточняется"
            $time = '[Дата уточняется]';
            $date = '';
        }else
        {// мероприятие с датой - выводим начало и окончание
            $time = Yii::app()->getDateFormatter()->format('HH:mm', $event->timestart).'-'.
                    Yii::app()->getDateFormatter()->format('HH:mm', $event->timeend);
            $date = '<nobr>'.$event->getFormattedDate().'</nobr>';
        }
        
        $containerOptions = array();
        if ( $this->questionary AND $event->hasMember($this->questionary->id) )
        {// @todo если пользователь участвует в событии - выделим его другим цветом 
            //$containerOptions['style'] = 'background-color:#468847;';
            //$name .= ' [вы участвуете]';
        }
        $itemOptions = array();
        if ( $event->isExpired() )
        {// @todo если пользователь участвует в событии - выделим его другим цветом 
            //$containerOptions['style'] = 'background-color:#aaa;';
        }
        
        if ( ( Yii::app()->user->checkAccess('Admin') OR Yii::app()->user->isGuest ) AND
              ( $this->userMode === 'user' ) )
        {// для гостей и админов показываем все открытые роли
            $vacancies = EventVacancy::model()->forEvent($event->id)->
                withStatus('active')->findAll();
        }elseif ( $this->userMode === 'user' AND Yii::app()->user->checkAccess('User') )
        {// для зарегистрированных пользователей - показываем только доступные роли
            $vacancies = $event->getAllowedVacancies($this->questionary->id);
        }else
        {// для заказчиков роли не показываем 
            $vacancies = array();
        }
        
        $result = array(
            'date'             => $date,
            'time'             => $time,
            'name'             => $name,
            //'description'      => $event->description,
            'description'      => $this->render('_timeLineEvent', array(
                'event'     => $event,
                'vacancies' => $vacancies,
            ), true),
            'itemOptions'      => $itemOptions,
            'dateOptions'      => array('style' => 'font-weight:300;font-size:1.5em;line-height:1.5em;'),
            'timeOptions'      => array('style' => 'font-weight:300;font-size:0.9em;color:#888;'),
            'containerOptions' => $containerOptions,
            //'iconOptions'      => array('class' => 'icon-star-o'),
            //'iconImage'        => $iconImage,
            //'iconImageOptions' => array(),
            'iconLink'         => $event->url,
            'iconLinkOptions'  => array(
                'data-toggle' => 'tooltip',
                'data-title'  => CHtml::encode($event->project->name),
            ),
        );
        return $result;
    }
}