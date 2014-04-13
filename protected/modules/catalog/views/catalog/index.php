<?php
/**
 * Страница каталога: отображает один из разделов каталога или список всех разделов
 */
/* @var $this CatalogController */

if ( $sectionid != 1 AND $section = CatalogSection::model()->findByPk($sectionid) )
{// Просматривается раздел каталога
    $this->breadcrumbs = array(
        CatalogModule::t('catalog') => '/catalog/catalog/faces',
    );
    $this->breadcrumbs[] = $section->name;
}else
{// просматриваются все разделы
   $this->breadcrumbs = array(
       CatalogModule::t('catalog'),
   );
}
?>
<div class="page-alternate">
    <div class="row-fluid">
        <?php 
        // Выводим страницу с фотоальбомами одним виджетом
        $this->widget('application.modules.catalog.extensions.CatalogData.CatalogData', array(
            'sectionid' => $sectionid,
        ));
        ?>
    </div>
</div>