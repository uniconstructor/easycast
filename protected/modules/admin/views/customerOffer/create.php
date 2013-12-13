<?php
/**
 * Создание нового коммерческого предложения
 * @var CustomerInvite $model
 */

// заголовок страницы
$title = 'Отправить коммерческое предложение';

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Отправить коммерческое предложение',
);

?>

<h2><?= $title; ?></h2>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>