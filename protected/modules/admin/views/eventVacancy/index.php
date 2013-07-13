<?php
$this->breadcrumbs=array(
	'Event Vacancies',
);

$this->menu=array(
	array('label'=>'Create EventVacancy','url'=>array('create')),
	array('label'=>'Manage EventVacancy','url'=>array('admin')),
);
?>

<h1>Event Vacancies</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
