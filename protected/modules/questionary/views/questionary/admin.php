<?php
/* @var $this QuestionaryController */
/* @var $model Questionary */

$this->breadcrumbs=array(
	'Questionaries'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Questionary', 'url'=>array('index')),
	array('label'=>'Create Questionary', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('questionary-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Questionaries</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'questionary-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'userid',
		'mainpictureid',
		'firstname',
		'lastname',
		'middlename',
		/*
		'birthdate',
		'gender',
		'timecreated',
		'timefilled',
		'timemodified',
		'height',
		'weight',
		'wearsizemin',
		'wearsizemax',
		'shoessize',
		'city',
		'cityid',
		'mobilephone',
		'homephone',
		'addphone',
		'vkprofile',
		'looktype',
		'haircolor',
		'eyecolor',
		'physiquetype',
		'isactor',
		'hasfilms',
		'isemcee',
		'isparodist',
		'istwin',
		'ismodel',
		'titsize',
		'chestsize',
		'waistsize',
		'hipsize',
		'isdancer',
		'dancerlevel',
		'hasawards',
		'isstripper',
		'striptype',
		'striplevel',
		'issinger',
		'singlevel',
		'voicetimbre',
		'ismusician',
		'musicianlevel',
		'issportsman',
		'isextremal',
		'isathlete',
		'hasskills',
		'hastricks',
		'haslanuages',
		'wantsbusinesstrips',
		'country',
		'countryid',
		'hasinshurancecard',
		'inshurancecardnum',
		'hasforeignpassport',
		'passportexpires',
		'passportserial',
		'passportnum',
		'passportdate',
		'passportorg',
		'addressid',
		'policyagreed',
		'status',
		'encrypted',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
