<?php
/**
 * Главная страница базы - "наши лица"
 */
/* @var $this CatalogController */

$this->breadcrumbs = array(
    CatalogModule::t('project_faces'),
);
?>
<div class="page-alternate">
    <div class="title-page">
        <h1 class="title"><?= CatalogModule::t('project_faces'); ?></h1>
    </div>
    <div class="row">
        <?php
        // список сервисов  
        $this->widget('ext.ECMarkup.EServiceList.EServiceList', array());
        ?>
    </div>
    <div style="margin-bottom:100px;">&nbsp;</div>
</div>