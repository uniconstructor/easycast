<?php
$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
	'Отправка коммерческих предложений' => array('/admin/customerOffer/admin'),
	'Предложение №'.$model->id => array('/admin/customerOffer/view', 'id'=>$model->id),
	'Редактировать',
);

$this->menu=array(
	array('label' => 'Список',   'url' => array('/admin/customerOffer/admin')),
	array('label' => 'Просмотр', 'url' => array('/admin/customerOffer/view','id' => $model->id)),
);
?>

<h1>Редактировать предложение №<?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>