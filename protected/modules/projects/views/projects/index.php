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
        $this->widget('ext.CdGridPreview.CdGridPreview', array(
            'dataProvider'     => $dataProvider,
            'listViewLocation' => 'bootstrap.widgets.TbListView',
            'listViewOptions'  => array(
                'template' => '{items}',
            ),
            'previewHtmlOptions' => array(
                'style' => 'min-height:150px;max-width:150px;min-width:150px;',
                'class' => 'ec-shadow-3px',
            ),
            'options' => array(
                'textClass'   => 'well og-details-text',
                'headerClass' => 'og-details-header ec-details-header',
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