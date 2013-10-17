<?php
/**
 * Вызывной лист
 */

// составляем верхнее меню навигации
$breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Проекты' => array('/admin/project'),
    $event->project->name => array('/admin/project/view','id' => $event->project->id),
    $event->name => array('/admin/projectEvent/view','id' => $event->id),
    'Вызывной лист',
);
$this->breadcrumbs = $breadcrumbs;

// отображаем сообщение о совершенном действии (если есть)
$this->widget('bootstrap.widgets.TbAlert');

// отображаем вызывной лист
$this->widget('reports.extensions.widgets.CallListReport.CallListReport', array(
    'event'  => $event,
    'report' => $report,
));


// отображаем ранее созданные вызывные листы на это мероприятие
$this->widget('reports.extensions.widgets.ReportList.ReportList', array(
    'reportClass'  => 'RCallList',
    'criteria'     => $reportListCriteria,
    'header'       => 'Ранее созданные вызывные для этого съемочного дня',
));