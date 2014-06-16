<?php

$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты',
);

$this->menu=array(
    array('label'=>'Анкеты на проверку','url'=>array('/admin/questionary/requests')),
    array('label'=>'Отложенные анкеты','url'=>array('/admin/questionary/delayed')),
    array('label'=>'Созданные анкеты','url'=>array('/admin/questionary/created')),
    array('label'=>'Редактор стандартных значений','url'=>array('/admin/standartValue/')),
    //array('label'=>'','url'=>array('/admin/projectMember/index', 'projectid'=>$model->id, 'type' => 'applications')),
);
?>

<h1>Анкеты</h1>