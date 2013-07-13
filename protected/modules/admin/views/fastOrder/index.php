<?php
$this->breadcrumbs=array(
    'Администрирование' =>array('/admin'),
	'Заказы',
);

$this->menu=array(
	array('label'=>'Ждут звонка', 'url'=>array('/admin/fastOrder/index/', 'display' => 'active')),
	array('label'=>'В обработке', 'url'=>array('/admin/fastOrder/index/', 'display' => 'pending')),
	array('label'=>'Обработаны', 'url'=>array('/admin/fastOrder/index/', 'display' => 'closed')),
	array('label'=>'Назначены мне', 'url'=>array('/admin/fastOrder/index/', 'display' => 'my')),
);

$this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
    ),
));
?>

<h1>Заказы</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
