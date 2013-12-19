<?php
/**
 * Страница администрирования списка разделов каталога
 * 
 * @todo отображать количество людей в каждом разделе
 * @todo сделать ссылку на редактирование условий выборки
 */

$this->breadcrumbs = array(
    'Администрирование' => array('/admin'),
    'Анкеты' => array('/admin/questionary'),
    'Разделы каталога',
);

$this->menu = array(
	//array('label'=>'List CatalogSection','url'=>array('index')),
	array('label' => 'Создать раздел каталога', 'url' => array('/admin/catalogSection/create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('catalog-section-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление разделами каталога</h1>

<?php // echo CHtml::link('Расширенный поиск','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
    <?php 
    $this->renderPartial('_search',array(
    	'model' => $model,
    ));
    ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id' => 'catalog-section-grid',
	'dataProvider' => $model->search(),
	'filter'       => $model,
	'columns' => array(
		//'id',
		
		// название раздела
		array(
            'name'  => 'name',
            'type'  => 'html',
            'value' => 'CHtml::link($data->name, Yii::app()->createUrl("/admin/catalogSection/view",array("id" => $data->id)))',
        ),
        // отображать ли в меню?
	    array(
	        'name'  => 'visible',
            'value' => 'Yii::t("coreMessages", $data->visible)',
            'headerHtmlOptions' => array('class' => 'span1'),
        ),

        //'order',
        //'count',
        
		// последняя колонка с инструментами
		array(
			'class'    => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{view} {update}',
		),
	),
)); ?>
