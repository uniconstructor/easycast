<?php

/**
 * Виджет "наши события" на главной странице (для участников)
 * 
 * @todo предусмотреть случай, в котором у нас нет ни одного предстоящего события или есть только одно событие
 * @todo получаем только те мероприятия в которых может участвовать пользователь (если он не гость)
 * @todo выровнять верстку, убрать разрыв div id=page
 */
class ECNewEvents extends CWidget
{
    /**
     * @var int - максимальное количество событий, выводимое в слайдере
     */
    public $eventLimit = 12;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        parent::init();
        Yii::import('projects.models.*');
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $events = array();
        // выводим только те события на которые идет набор
        $crirteria = new CDbCriteria();
        $crirteria->compare('status', 'active');
        $crirteria->limit = $this->eventLimit;
        $crirteria->order = "`timestart` DESC";
        
        if ( ! $records = ProjectEvent::model()->findAll($crirteria) )
        {// предстоящих событий нет - ничего не выводим
            return;
        }
        
        // выводим по два события в строчку
        $element = '';
        foreach ( $records as $record )
        {// для каждого мероприятия создаем отдельный блок html-кода
            $events[] = $this->render('_event', array('data' => $record), true);
        }
        
        $this->render('events', array('events' => $events));
    }
}