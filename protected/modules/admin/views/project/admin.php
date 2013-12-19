<?php

/* @var $this ProjectController */
$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
	//'Проекты' => array('/admin/project'),
	'Проекты',
);

$this->menu = array(
	//array('label'=>'Список проектов','url'=>array('/admin/project')),
	array('label' => 'Создать проект','url' => array('/admin/project/create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('project-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Поиск и редактирование проектов</h1>

<p>
При поиске вы можете использовать операторы сравнения (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
или <b>=</b>) в начале строки.
</p>

<?php // echo CHtml::link('Расширенный поиск','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search', array(
	'model' => $model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'           => 'project-grid',
	'dataProvider' => $model->search(),
	'filter'       => $model,
	'columns' => array(
		//'id',
		array(
            'name'  => 'name',
            'type'  => 'html',
            'value' => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/project/view",array("id" => $data->id)))',
        ),
        'statustext',
		/*'type',
		'description',
		'galleryid',
		'timestart',
		'timeend',
		'timecreated',
		'timemodified',
		'leaderid',
		'customerid',
		'orderid',
		'isfree',
		'memberscount',
		*/
		array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{view} {update}',
		),
	),
)); ?>
