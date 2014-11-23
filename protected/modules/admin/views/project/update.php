<?php
/**
 * Страница редактирования проекта
 */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
	'Проекты'           => array('/admin/project/admin'),
	$model->name        => array('/admin/project/view', 'id' => $model->id),
	'Редактировать',
);

$this->menu = array(
	array('label' => 'Список проектов', 'url' => array('/admin/project/admin')),
	array('label' => 'Создать проект', 'url' => array('/admin/project/create')),
	array('label' => 'Просмотр проекта', 'url' => array('/admin/project/view', 'id' => $model->id)),
);

//echo '<div class="page"><div class="container">';
?>
<h1><?= $model->name; ?></h1>
<?php 
echo $this->renderPartial('_form', array(
    'model' => $model,
));
//echo '</div></div>';