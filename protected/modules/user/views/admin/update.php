<?php
$this->breadcrumbs=array(
	(UserModule::t('Users'))=>array('admin'),
	$model->username=>array('view','id'=>$model->id),
	(UserModule::t('Update')),
);
$this->menu=array(
    array('label'=>UserModule::t('Create User'), 'url'=>array('create')),
    array('label'=>UserModule::t('View User'), 'url'=>array('view','id'=>$model->id)),
    array('label'=>QuestionaryModule::t('edit_questionary'), 'url'=>array('/questionary/questionary/update/id/'.$model->questionary->id)),
    array('label'=>UserModule::t('List User'), 'url'=>array('admin')),
    //array('label'=>UserModule::t('List User'), 'url'=>array('/user')),
);
?>

<h1><?php echo UserModule::t('Update User')." ".$model->id; ?></h1>

<?php
	echo $this->renderPartial('_form', array('model'=>$model));
?>