<?php
/**
 * Главная страница базы - "наши лица"
 */
/* @var $this CatalogController */

$this->breadcrumbs = array(
    CatalogModule::t('project_faces'),
);

// список сервисов на главной (только для заказчиков)  
$this->widget('ext.ECMarkup.EServiceList.EServiceList', array());

?>
<div style="margin-bottom:100px;">&nbsp;</div>