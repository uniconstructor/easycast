<?php
/**
 * Отображение стандартных значений
 */

$this->breadcrumbs=array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Редактор стандартных значений',
);
?>

<h1>Редактор стандартных значений</h1>

<h4><?php echo $header; ?></h4>

<?php 
$this->menu=array(
    array('label'=>'Музыкальные ВУЗы','url'=>array('/admin/standartValue/index', 'class'=>'university', 'type'=>'music')),
    array('label'=>'Театральные ВУЗы','url'=>array('/admin/standartValue/index', 'class'=>'university', 'type'=>'theatre')),
    array('label'=>'Тип внешности','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'looktype')),
    array('label'=>'Цвет волос','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'haircolor')),
    array('label'=>'Цвет глаз','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'eyecolor')),
    array('label'=>'Телосложение','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'physiquetype')),
    array('label'=>'Доп. характеристики','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'addchar')),
    array('label'=>'Виды танца','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'dancetype')),
    array('label'=>'Типы вокала','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'vocaltype')),
    array('label'=>'Тембры голоса','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'voicetimbre')),
    array('label'=>'Уровень вокала','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'singlevel')),
    array('label'=>'Муз. инструменты','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'instrument')),
    array('label'=>'Виды спорта','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'sporttype')),
    array('label'=>'Экстремальный спорт','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'extremaltype')),
    array('label'=>'Умения и навыки','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'skill')),
    array('label'=>'Иностранные языки','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'language')),
    array('label'=>'Уровень владения языком','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'languagelevel')),
    //array('label'=>'Размер одежды','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'wearsize')),
    array('label'=>'Размер обуви','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'shoessize')),
    array('label'=>'Размер груди','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'titsize')),
    //array('label'=>'Рейтинг','url'=>array('/admin/standartValue/index', 'class'=>'activity', 'type'=>'rating')),
);

//Yii::import('application.modules.admin.extensions.QDefaults.QDefaults');

// Выводим список значений
$this->widget('admin.extensions.QDefaults.QDefaults', array(
    'valueClass' => $class,
    'valueType'  => $type,
));


