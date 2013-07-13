<?php
/**
 * @var CatalogSection $model 
 */

$this->breadcrumbs=array(
	'Администрирование' =>array('/admin'),
    'Анкеты' =>array('/admin/questionary'),
    'Разделы каталога' => array('/admin/catalogSection/admin'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Список разделов','url'=>array('/admin/catalogSection/admin')),
	//array('label'=>'Create CatalogSection','url'=>array('create')),
	array('label'=>'Редактировать раздел','url'=>array('update','id'=>$model->id)),
	//array('label'=>'Delete CatalogSection','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Manage CatalogSection','url'=>array('admin')),
);
?>

<h1>Раздел "<?php echo $model->name; ?>"</h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		//'parentid',
		//'scopeid',
		'name',
		'shortname',
		//'lang',
		//'galleryid',
		//'content',
		'order',
		//'count',
		'visible',
	),
)); ?>
<br>
<br>
<h3>Условия выборки анкет в раздел</h3>
<?php $this->widget('application.modules.admin.extensions.ScopesList.ScopesList', array('scope' => $model->scope)); ?>
<br>
<br>
<h3>Вкладки</h3>
<ul>
<?php
// Выводим все вкладки
Yii::import('catalog.models.*');

foreach ( $model->instances as $tabInstance )
{
    echo '<li>'.$tabInstance->tab->name;
    
    /*echo '<ul>'
        echo '<li>'.$this->widget('admin.extensions.ScopesList.ScopesList', array(), true).'</li>';
    echo '<ul>';*/
    echo '</li>';
    $this->widget('application.modules.admin.extensions.ScopesList.ScopesList', array('scope' => $tabInstance->tab->scope));
}
?>
</ul>