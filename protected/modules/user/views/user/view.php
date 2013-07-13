<?php
$this->breadcrumbs=array(
	UserModule::t('Users')=>array('index'),
	$model->username,
);
$this->layout='//layouts/column2';
$this->menu=array(
    array('label'=>QuestionaryModule::t('edit_questionary'), 'url'=>array('/questionary/questionary/update/id/'.$model->questionary->id)),
    ((UserModule::isAdmin())
        ?array('label'=>UserModule::t('List User'), 'url'=>array('index'))
        :array()),
);
?>
<h1><?php echo UserModule::t('View User').' "'.Yii::app()->getModule('user')->user($model->id)->fullname.'"'; ?></h1>
<?php 

// For all users
	$attributes = array(
			'username',
	);
	
	array_push($attributes,
		'create_at',
		array(
			'name' => 'lastaccess',
			'value' => (($model->lastaccess!='0000-00-00 00:00:00')?$model->lastaccess:UserModule::t('Not visited')),
		)
	);
			
	$this->widget('bootstrap.widgets.TbDetailView', array(
		'data'=>$model,
		'attributes'=>$attributes,
	));

?>
