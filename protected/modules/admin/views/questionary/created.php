<?php
/**
 * Отображение введенных пользователем анкет
 */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Созданные анкеты',
);

?>

<h1>Созданные анкеты</h1>

<?php 

$this->widget('admin.extensions.QCreated.QCreated');

?>