<?php
/**
 * Отображение введенных пользователем анкет
 */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Анкеты'            => array('/admin/questionary'),
    'Созданные анкеты',
);


$moduleClass = get_class(Yii::app()->getModule('user'));
$admins = $moduleClass::getAdminList();
$menuItems = array();
foreach ( $admins as $id => $name )
{
    $menuItems[] = array(
        'label' => $name,
        'url'   => array('/admin/questionary/created', 'id' => $id)
    );
}
$this->menu = $menuItems;

?>

<h1>Созданные анкеты</h1>

<?php 

$this->widget('admin.extensions.QCreated.QCreated', array('userId' => $userId));

?>