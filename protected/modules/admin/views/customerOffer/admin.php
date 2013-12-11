<?php
/**
 * Список отправленных коммерческих предложений
 */

$this->breadcrumbs = array(
	'Администрирование' => array('/admin'),
	'Коммерческие предложения',
);

$this->menu = array(
	array('label' => 'Создать', 'url' => array('/admin/customerOffer/create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('customer-offer-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1>Отправка коммерческих предложений</h1>

<?php /* echo CHtml::link('Advanced Search','#',array('class'=>'search-button btn')); */ ?>
<div class="search-form" style="display:none">
<?php
$this->renderPartial('_search', array(
	'model' => $model,
));
?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'           => 'customer-offer-grid',
	'dataProvider' => $model->search(),
	'filter'       => $model,
	'columns' => array(
		'id',
		//'objecttype',
		//'objectid',
		'email',
        'name',
        // кто отправил ссылку
        array(
            'name'   => 'managerid',
            'value'  => '$data->manager->fullname',
            'header' => '<b>Кто отправил</b>',
            'type'   => 'html',
        ),
        // время создания
        array(
            'name'    => 'timecreated',
            'value'   => '($data->timecreated ? date("Y-m-d H:i", $data->timecreated): "Не отправлено")',
            'header'  => '<b>Время создания</b>',
            'type'    => 'html',
        ),
        // время использования
		'timeused',
        array(
            'name'    => 'timeused',
            'value'   => '($data->timeused ? date("Y-m-d H:i", $data->timeused): "Не использовано")',
            'header'  => '<b>Время использования</b>',
            'type'    => 'html',
        ),
		//'comment',
		//'userid',
		/*array(
			'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{view} {update}',
            'viewButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/view", array("id" => $data->questionaryid))',
            'updateButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/update", array("id" => $data->questionaryid))',
		),*/
	),
)); ?>
