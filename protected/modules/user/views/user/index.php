<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
	UserModule::t("Users"),
);
if(UserModule::isAdmin()) {
	$this->layout='//layouts/column2';
	$this->menu=array(
	    array('label'=>UserModule::t('Create User'), 'url'=>array('/user/admin/create')),
	    array('label'=>UserModule::t('Manage Users'), 'url'=>array('/user/admin')),
	    //array('label'=>UserModule::t('Manage Profile Field'), 'url'=>array('profileField/admin')),
	);
}
?>

<h1><?php echo UserModule::t("List User"); ?></h1>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name' => 'username',
			'type'=>'raw',
			'value' => 'CHtml::link(CHtml::encode($data->username),array("user/view","id"=>$data->id))',
		),
	    array(
	        'name' => 'Имя',
	        'type'=>'raw',
	        'value' => 'CHtml::link(CHtml::encode($data->questionary->lastname." ".$data->questionary->firstname." ".$data->questionary->middlename),array("user/view","id"=>$data->id))',
	    ),
		'create_at',
		'lastaccess',
	),
)); ?>
