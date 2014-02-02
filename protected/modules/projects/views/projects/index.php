<?php
/**
 * Главная страница со списком всех проектов
 * @todo вынести список всех проектов в отдельный виджет
 */
/* @var $this ProjectsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
	ProjectsModule::t('projects'),
);
?>

<div>
<?php 

if ( Yii::app()->params['useCSS3'] )
{// список проектов в новом виде
    $dataProvider->pagination = false;
    $this->widget('ext.CdGridPreview.CdGridPreview', array(
        'dataProvider'     => $dataProvider,
        'listViewLocation' => 'bootstrap.widgets.TbListView',
        //'htmlOptions'      => array('id' => 'og-grid'),
        'listViewOptions'  => array(
            'template' => '{items}',
        ),
        'options' => array(
            'textClass'   => 'well og-details-text',
            'headerClass' => 'og-details-header ec-details-header',
            //'detailsClass' => 'well',
        ),
    ));
}else
{// старый вариант верстки
    $this->widget('bootstrap.widgets.TbThumbnails', array(
        'dataProvider' => $dataProvider,
        'template'     => "{items}{pager}",
        'itemView'     => '_project',
    ));
}

?>
</div>