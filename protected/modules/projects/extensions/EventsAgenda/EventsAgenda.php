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
 */
class EventsAgenda extends CWidget
{
    /**
     * @var bool - отображать ли активные события?
     */
    public $displayActive   = false;
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
    public $eventLimit      = 0;
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
        if ( ! $this->displayActive AND ! $this->displayFinished )
        {// если не отображать ни активные ни завершенные события - то список всегда будет пустым
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
        // получаем список последних событий с сайта
        $this->loadEvents();
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        if ( $this->displayMode === 'thumbnails' )
        {
            $this->widget('bootstrap.widgets.TbThumbnails', array(
                'dataProvider' => $this->dataProvider,
                'itemView'     => '_thumbnailEvent',
                'emptyText'    => 'nodata',
                'template'     => '{items}',
            ));
        }else
        {
            $this->widget('ext.CdVerticalTimeLine.CdVerticalTimeLine', array(
                'events' => $this->events,
            ));
        }
    }
    
    /**
     * Получить список отображаемых событий
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
            $pagination = array('pageSize' => $this->eventLimit);
        }
        if ( ! $this->displayCastings )
        {
            $criteria->compare('virtual', 0);
        }
        
        $criteria->addInCondition('status', $statuses);
        $criteria->order = "`timestart` DESC";
        
        if ( $this->displayMode === 'thumbnails' )
        {
            $this->dataProvider = new CActiveDataProvider('ProjectEvent', array(
                'criteria'   => $criteria,
                'pagination' => $pagination,
            ));
        }else
        {
            $this->events = $this->getTimeLineEvents($criteria);
        }
    }
    
    /**
     * Получить массив событий для использования в элементе CdVerticalTimeLine
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
     * @param ProjectEvent $event
     * @return array
     * 
     * @todo для подтвержденных участников добавлять место сбора, доп. информацию и все остальное
     */
    protected function getTimeLineEvent($event)
    {
        
        
        $name = CHtml::link($event->project->name, $event->url);
        if ( $event->nodates )
        {// мероприятие без конкретной даты - пишем "дата уточняется"
            $time = '[Дата уточняется]';
            $date = '';
        }else
        {
            $time = Yii::app()->getDateFormatter()->format('HH:mm', $event->timestart).'-'.
                    Yii::app()->getDateFormatter()->format('HH:mm', $event->timeend);
            $date = '<nobr>'.$event->getFormattedDate().'</nobr>';
        }
        
        $containerOptions = array();
        if ( $this->questionary AND $event->hasMember($this->questionary->id) )
        {// если пользователь участвует в событии - выделим его другим цветом 
            $containerOptions['style'] = 'background-color:#468847;';
            $name .= ' (вы участвуете)';
        }
        $itemOptions = array();
        if ( $event->isExpired() )
        {// если пользователь участвует в событии - выделим его другим цветом 
            //$itemOptions['style'] = 'opacity:0.8;';
        }
        $iconImage = $event->project->getAvatarUrl();
        
        $result = array(
            'date'             => $date,
            'time'             => $time,
            'name'             => $name,
            'description'      => $event->description,
            'itemOptions'      => $itemOptions,
            'dateOptions'      => array('style' => 'font-weight:300;font-size:1.5em;line-height:1.5em;'),
            'timeOptions'      => array('style' => 'font-weight:300;font-size:0.9em;color:#888;'),
            'containerOptions' => $containerOptions,
            //'iconOptions' => array('class' => 'cbp_tmicon-phone'),
            'iconImage'        => $iconImage,
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