<?php
/* @var $this QuestionaryController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	QuestionaryModule::t('catalog'),
);

$this->menu=array(
	//array('label'=>QuestionaryModule::t('create_questionary'), 'url'=>array('create')),
	//array('label'=>QuestionaryModule::t('manage_questionary'), 'url'=>array('admin')),
);
?>

<h1><?php QuestionaryModule::t('questionaries'); ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
