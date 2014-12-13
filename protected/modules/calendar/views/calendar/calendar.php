<?php
/**
 * Главная страница календаря
 */
/* @var $this CalendarController */

$this->pageTitle   = Yii::t('coreMessages', 'calendar');
$this->breadcrumbs = array(
    Yii::t('coreMessages', 'calendar'),
);
?>
<br><br><br><br><br>
<?php 
// Выводим календарь со всеми событиями
$this->widget('ext.efullcalendar.EFullCalendar', array(
        //'themeCssFile' => 'dot-luv/theme.css',
        //'themeCssFile' => 'css/jqueryui/flick/jquery-ui.css',
        'lang'    => 'ru',
        'options' => array(
            'allDayText' => Yii::t('coreMessages', 'all_day'),
            // Показывать полоску для событий целого дня
            'allDaySlot' => true,
            // Формат сетки времени при просмотре недели
            'axisFormat' => 'HH:mm',
            // Формат времени для одного события
            'timeFormat' => 'HH:mm{ - HH:mm}',
            // самый ранний час
            'firstHour'  => '7',
            // отображать выходные
            'weekends'   => true,
            // заголовок
            'header' => array(
                // стрелочки слева
                'left'  => 'prev,next',
                // заголовок
                'center' => 'title',
                // справа день, неделя, месяц
                'right'  => 'agendaDay,agendaWeek,month'
            ),
            'events' => array(
                'url' => Yii::app()->createUrl('/calendar/calendar/getEvents'),
            ),
            /*'columnFormat' => array(
                'day' => 'dddd dd MM',
            ),*/
    )));