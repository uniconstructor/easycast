<?php
$this->breadcrumbs = array(
	'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Разделы каталога' => array('/admin/catalogSection/admin'),
	'Создать',
);

$this->menu = array(
	array('label' => 'Список разделов', 'url' => array('/admin/catalogSection/admin')),
);
?>

<h1>Создать раздел каталога</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>