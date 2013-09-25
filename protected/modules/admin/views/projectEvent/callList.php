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

// отображаем вызывной лист
$this->widget('admin.extensions.CallList.CallList', array(
    'objectId' => $event->id,
));