<?php
/**
 * Страница отображения одного приглашения заказчика
 * @var CustomerInvite $model
 * 
 * @todo добавить предпросмотр отправляемого письма
 */
$this->breadcrumbs = array(
    'Администрирование' => array('/admin/index'),
    'Отправка коммерческих предложений' => array('/admin/customerOffer/admin'),
);

$this->breadcrumbs[] = 'Предложение №'.$model->id.' ('.$model->name.')';

// отображаение оповещений
$this->widget('bootstrap.widgets.TbAlert');

?>
<h1>Приглашение №<?php echo $model->id; ?></h1>
<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data' => $model,
	'attributes' => array(
		'id',
		//'objecttype',
		'objectid',
		'email',
		'name',
		'managerid',
		'timecreated',
		'timeused',
		'comment',
		'userid',
	),
));
?>
