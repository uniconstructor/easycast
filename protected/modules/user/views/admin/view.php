<?php
/**
 * Страница просмотра пользователя
 */
// навигация
$this->breadcrumbs = array(
	UserModule::t('Users') => array('admin'),
	$model->username,
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
    array('label' => UserModule::t('Update User'), 'url' => array('update', 'id' => $model->id)),
    $qLink,
    array('label' => UserModule::t('Delete User'), 'url' => '#',
        'linkOptions' => array(
            'submit' => array(
                    'delete',
                    'id' => $model->id,
                ),
            'confirm' => UserModule::t('Are you sure to delete this item?'),
            'csrf' => true)),
    array('label' => UserModule::t('List User'), 'url' => array('admin')),
);
?>
<h1><?php echo UserModule::t('View User').' "'.Yii::app()->getModule('user')->user($model->id)->fullname.'"'; ?></h1>

<?php
 
	$attributes = array(
		'id',
		'username',
	);
	
	array_push($attributes,
		'password',
		'email',
		'activkey',
		'create_at',
		'lastaccess',
		array(
			'name' => 'superuser',
			'value' => User::itemAlias("AdminStatus", $model->superuser),
		),
		array(
			'name' => 'status',
			'value' => User::itemAlias("UserStatus", $model->status),
		)
	);
	
	$this->widget('bootstrap.widgets.TbDetailView', array(
		'data'       => $model,
		'attributes' => $attributes,
	));
	