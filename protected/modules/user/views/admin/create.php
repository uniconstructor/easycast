<?php
$this->breadcrumbs = array(
	UserModule::t('Users') => array('admin'),
	UserModule::t('Create'),
);

$this->menu=array(
    array('label'=>UserModule::t('List User'), 'url' => array('admin')),
    //array('label'=>UserModule::t('List User'), 'url' => array('/user')),
);
?>
<h2><?php echo 'Новый участник'; ?></h2>

<?php
	echo $this->renderPartial('_form', array('model' => $model, 'profile' => $profile));
?>