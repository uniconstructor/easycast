<?php
$this->breadcrumbs=array(
	'Project Members'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ProjectMember','url'=>array('index')),
	array('label'=>'Manage ProjectMember','url'=>array('admin')),
);
?>

<h1>Create ProjectMember</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>