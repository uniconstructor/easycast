<?php
$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Редактор стандартных значений' => array('/admin/standartValue/index', 'class'=>$class, 'type'=>$type),
    'Добавить ВУЗ',
);

/*$this->menu=array(
    array('label'=>'List QActivityType','url'=>array('index')),
    array('label'=>'Manage QActivityType','url'=>array('admin')),
);*/

if ( $type == 'music' )
{
    $header = 'Добавить музыкальный ВУЗ';
}
if ( $type == 'theatre' )
{
    $header = 'Добавить театральный ВУЗ';
}
?>

<h1><?php echo $header; ?></h1>

<?php echo $this->renderPartial('_formUniversity', array('model'=>$model, 'class' => $class,'type' => $type)); ?> 