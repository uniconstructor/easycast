<?php

// Выводим календарь со всеми событиями
$this->widget('ext.efullcalendar.EFullCalendar', array(
                //'themeCssFile'=>'dot-luv/theme.css',
                //'themeCssFile'=>'cupertino/theme.css',
                'lang'    => 'ru',
                'options' => array(
                    //'allDayText' => CalendarModule::t('all_day'),
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