<?php
/**
 * Страница отображения расширенной формы поиска (во всю страницу которая)
 */
/* @var $this SearchController */

$this->pageTitle = 'Подбор актеров - easyCast';

$this->breadcrumbs = array(
    'Поиск',
);

// получаем корневой раздел каталога ("вся база") для того чтобы искать по всем доступным анкетам
$rootSection = CatalogSection::model()->findByPk(1);
// виджет расширенной формы поиска (по всей базе)
$this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
    'searchObject' => $rootSection,
    'mode'         => 'filter',
));

//CVarDumper::dump( CatalogModule::getFilterSearchData($namePrefix, $sectionId) );
?>
