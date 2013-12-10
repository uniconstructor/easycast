<?php
/**
 * Страница обновления пользователя
 */
$this->breadcrumbs = array(
	(UserModule::t('Users')) => array('admin'),
	$model->username => array('view','id' => $model->id),
	(UserModule::t('Update')),
);

// проверка на случай ошибки в базе
if ( isset($model->questionary->id) )
{
    $qLink = array(
        'label' => QuestionaryModule::t('edit_questionary'),
        'url'   => array('/questionary/questionary/update/id/'.$model->questionary->id),
    );
}else
{
    $qLink = array(
        'label' => 'Ошибка: анкета не найдена. Удалите и пересоздайте этого пользователя.',
    );
}

$this->menu = array(
    array('label' => UserModule::t('Create User'), 'url' => array('create')),
    array('label' => UserModule::t('View User'), 'url' => array('view','id' => $model->id)),
    $qLink,
    array('label' => UserModule::t('List User'), 'url' => array('admin')),
);
?>

<h1><?php echo UserModule::t('Update User')." ".$model->id; ?></h1>

<?php
	echo $this->renderPartial('_form', array('model' => $model));
?>