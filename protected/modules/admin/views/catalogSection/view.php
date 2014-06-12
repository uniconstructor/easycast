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

<div class="row-fluid">
    <h1>Раздел "<?php echo $model->name; ?>"</h1>
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
        <?php
        // для создания раздела каталога используем все доступные фильтры 
        $filters = CatalogFilter::model()->findAll("`id` > '1'");
        // критерии поиска для раздела
        $this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
            'searchObject' => $model,
            'mode'         => 'section',
            'filters'      => $filters,
            'displayTitle' => false,
            'dataSource'   => 'db',
            'searchUrl'    => '/admin/catalogSection/setSearchData/id/'.$model->id,
            'clearUrl'     => '/admin/catalogSection/clearSearchData',
        ));
        ?>
        <?php 
        //CVarDumper::dump($model->searchFilters);
         
        // форма добавления/удаления критериев поиска
        $this->widget('admin.extensions.SearchFilterManager.SearchFilterManager', array(
            'searchObject' => $model,
        ));
        ?>
</div>
<?php 

//CVarDumper::dump($_POST, 10, true);

?>

