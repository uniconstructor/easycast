<?php
$this->breadcrumbs=array(
    'Администрирование'=>array('/admin'),
	'Заказы'=>array('/admin/fastOrder/index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Список заказов','url'=>array('index')),
	//array('label'=>'Пометить выполненным','url'=>array('update','id'=>$model->id)),
	// array('label'=>'Удалить заказ','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Поиск','url'=>array('admin')),
);
?>

<h1>Заказ №<?php echo $model->id; ?></h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'timecreated',
		'name',
		'phone',
		'email',
		'status',
		'comment',
		'ourcomment',
		'solverid',
		'customerid',
	),
)); 

// @todo сделать более удобный вывод
if ( $model->type == 'normal' )
{// выводим всех актеров, которые есть в заказе
    if ( $orderData = $model->loadOrderData() AND is_array($orderData) )
    {
        echo '<h3>Список актеров в заказе</h3>';
        echo '<ul>';
        foreach ( $orderData['users'] as $id )
        {
            $questionary = Questionary::model()->findByPk($id);
            $url = Yii::app()->createUrl('/questionary/questionary/view', array('id' => $id));
            $link = CHtml::link($questionary->fullname, $url);
            echo '<li>'.$link.'</li>';
        }
        echo '</ul>';
    }
}
?>
