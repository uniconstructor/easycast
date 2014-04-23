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

<?php 
// форма создания приглашения
echo $this->renderPartial('_form', array('model' => $model));

$searchModel = CustomerOffer::model();
$this->widget('bootstrap.widgets.TbGridView', array(
    'id'           => 'customer-offer-grid',
    'dataProvider' => $searchModel->search(),
    'filter'       => $searchModel,
    'columns' => array(
        'id',
        'email',
        'name',
        // кто отправил ссылку
        array(
            'name'   => 'managerid',
            'value'  => '$data->manager->fullname',
            'header' => '<b>Кто отправил</b>',
            'type'   => 'html',
        ),
        // время создания
        array(
            'name'    => 'timecreated',
            'value'   => '($data->timecreated ? date("Y-m-d H:i", $data->timecreated): "Не отправлено")',
            'header'  => '<b>Время создания</b>',
            'type'    => 'html',
        ),
        // время использования
        array(
            'name'    => 'timeused',
            'value'   => '($data->timeused ? date("Y-m-d H:i", $data->timeused): "Не использовано")',
            'header'  => '<b>Время использования</b>',
            'type'    => 'html',
        ),
        //'comment',
        //'userid',
        /*array(
         'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{view} {update}',
            'viewButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/view", array("id" => $data->questionaryid))',
            'updateButtonUrl' => 'Yii::app()->controller->createUrl("/questionary/questionary/update", array("id" => $data->questionaryid))',
        ),*/
    ),
));