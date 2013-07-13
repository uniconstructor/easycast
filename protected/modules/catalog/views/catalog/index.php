<?php
/* @var $this CatalogController */

if ( $sectionid != 1 AND $section = CatalogSection::model()->findByPk($sectionid) )
{// Просматривается раздел каталога
    $this->breadcrumbs=array(
        CatalogModule::t('catalog') => '/catalog',
    );
    $this->breadcrumbs[] = $section->name;
}else
{
   $this->breadcrumbs=array(
       CatalogModule::t('catalog'),
   );
}
?>
<!--h1><?php echo CatalogModule::t('catalog'); ?></h1-->
<div class="span12" style="margin-left:0px;">
<?php 

// Выводим страницу с фотоальбомами одним виджетом
$this->widget('application.modules.catalog.extensions.CatalogData.CatalogData', array(
        'sectionid' => $sectionid,
    )
);

?>
</div>