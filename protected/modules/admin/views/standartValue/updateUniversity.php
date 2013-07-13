<?php
$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Редактор стандартных значений' => array('/admin/standartValue/index', 'class'=>$class, 'type'=>$type),
    'Правка',
);

/*
$this->menu=array(
    array('label'=>'List QActivityType','url'=>array('index')),
    array('label'=>'Create QActivityType','url'=>array('create')),
    array('label'=>'View QActivityType','url'=>array('view','id'=>$model->id)),
    array('label'=>'Manage QActivityType','url'=>array('admin')),
);*/
?>

<h1>Редактировать ВУЗ "<?php echo $model->name; ?>"</h1>

<?php echo $this->renderPartial('_formUniversity',array('model'=>$model, 'class' => $class,'type' => $type)); ?> 