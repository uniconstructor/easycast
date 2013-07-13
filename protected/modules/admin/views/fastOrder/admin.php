<?php
$this->breadcrumbs=array(
	'Срочные заказы'=>array('index'),
	'Управление',
);

$this->menu=array(
	array('label'=>'Список заказов','url'=>array('index')),
	//array('label'=>'Create FastOrder','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('fast-order-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Список заказов</h1>


<?php //echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'fast-order-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		//'id',
		'timecreated',
		//'name',
		//'phone',
		//'email',
		'status',
		/*
		'comment',
		'ourcomment',
		'solverid',
		'customerid',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
