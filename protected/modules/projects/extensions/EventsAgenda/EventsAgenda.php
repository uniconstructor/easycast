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
    public $timersLimit     = 0;
    
    /**
     * @var string - режим просмотра: заказчик (customer) или участник (user)
     */
    protected $userMode;
    /**
     * @var CActiveDataProvider - источник данных для получения списка событий
     */
    protected $dataProvider;
    
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
        $this->loadEvents();
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->render('events');
    }
    
    /**
     * Получить список отображаемых событий
     * @return ProjectEvent
     */
    protected function loadEvents()
    {
        $statuses = array();
        $criteria = new CDbCriteria();
        
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
        }else
        {
            $pagination = false;
        }
        if ( ! $this->displayCastings )
        {
            $criteria->compare('virtual', 0);
        }
        
        $criteria->addInCondition('status', $statuses);
        $criteria->order = "`timestart` DESC";
        
        $this->dataProvider = new CActiveDataProvider('ProjectEvent', array(
            'criteria'   => $criteria,
            'pagination' => $pagination,
        ));
    }
}