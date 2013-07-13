<?php
$this->breadcrumbs=array(
	'Project Members'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List ProjectMember','url'=>array('index')),
	array('label'=>'Create ProjectMember','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('project-member-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Project Members</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'project-member-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'memberid',
		'vacancyid',
		'timecreated',
		'timemodified',
		'managerid',
		/*
		'request',
		'responce',
		'timestart',
		'timeend',
		'status',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
