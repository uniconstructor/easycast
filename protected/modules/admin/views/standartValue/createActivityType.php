<?php
$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Редактор стандартных значений' => array('/admin/standartValue/index', 'class'=>$class, 'type'=>$type),
    'Добавить',
);

/*$this->menu=array(
    array('label'=>'List QActivityType','url'=>array('index')),
    array('label'=>'Manage QActivityType','url'=>array('admin')),
);*/
?>

<h1>Добавить стандартное значение</h1>

<?php echo $this->renderPartial('_formActivityType', array('model'=>$model, 'class' => $class,'type' => $type)); ?> 