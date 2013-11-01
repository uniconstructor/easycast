<?php
/**
 * Страница просмотра раздела каталога
 * @todo вывести количество людей в разделе
 */
/* @var $model CatalogSection */


$this->breadcrumbs = array(
	'Администрирование' => array('/admin'),
    'Анкеты'            => array('/admin/questionary'),
    'Разделы каталога'  => array('/admin/catalogSection/admin'),
	$model->name,
);

$this->menu = array(
	array(
	    'label' => 'Список разделов', 
	    'url'   => array('/admin/catalogSection/admin')),
	array(
	    'label' => 'Создать раздел',
	    'url'   => array('/admin/catalogSection/create')),
	array(
	    'label' => 'Редактировать раздел',
	    'url'   => array('/admin/catalogSection/update', 'id' => $model->id)),
	array(
	    'label' => 'Удалить раздел', 
	    'url'   => '#', 
	    'linkOptions' => array(
	           'submit' => array('delete', 'id' => $model->id),
	               'confirm' => 'Удалить раздел?',
	               'csrf'    => true,
	           )
	    ),
);
?>

<div class="span12">
    <div class="row" style="text-align: center;">
        <h1>Раздел "<?php echo $model->name; ?>"</h1>
    </div>
    <div class="span5">
        <?php 
        // данные самого раздела
        $this->widget('bootstrap.widgets.TbDetailView', array(
        	'data' => $model,
        	'attributes' => array(
        		'id',
        		'shortname',
        		//'count',
        		array(
                    'label' => 'Отобразить категорию?',
                    'value' => Yii::t('coreMessages', $model->visible),
                ),
        	),
        )); 
        ?>
    </div>
        <?php 
        //CVarDumper::dump($model->searchFilters);
         
        // форма добавления/удаления критериев поиска
        $this->widget('admin.extensions.SearchFilterManager.SearchFilterManager', array(
            'searchObject' => $model,
        ));
        // критерии поиска для раздела
        
        ?>
</div>
<?php 

CVarDumper::dump($_POST, 10, true);

?>

