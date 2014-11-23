<?php
/**
 * 
 */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
	'Проекты'           => array('/admin/project/admin'),
	'Новый проект',
);
$this->menu = array(
	array('label' => 'Список проектов', 'url' => array('/admin/project/admin')),
);

//echo '<div class="page"><div class="container">';
?>
<h1>Новый проект</h1>
<?php 
echo $this->renderPartial('_form', array(
    'model' => $model,
));
//echo '</div></div>';