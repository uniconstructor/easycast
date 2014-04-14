<?php
/**
 * Страница отображения расширенной формы поиска (во всю страницу которая)
 * @todo языковые строки
 */
/* @var $this SearchController */

$this->pageTitle = 'Поиск актеров - easyCast';

$this->breadcrumbs = array(
    'Поиск',
);
?>
<div class="page-alternate">
    <div class="container">
        <div class="title-page">
            <h1 class="title">Поиск</h1>
            <h4 class="intro-description">Укажите нужные критерии</h4>
        </div>
    </div>
    <div class="row-fluid" style="padding:20px;">
        <?php
        // получаем корневой раздел каталога ("вся база") для того чтобы искать по всем доступным анкетам
        $rootSection = CatalogSection::model()->findByPk(1);
        // виджет расширенной формы поиска (по всей базе)
        $this->widget('catalog.extensions.search.QSearchForm.QSearchForm', array(
            'searchObject' => $rootSection,
            'mode'         => 'filter',
            // после отправки ajax-запроса поиска перенаправляет пользователя на страницу с фильтрами
            'redirectUrl'  => '/catalog/catalog/search',
            'refreshDataOnChange' => false,
        ));
        //CVarDumper::dump( CatalogModule::getFilterSearchData($namePrefix, $sectionId) );
        ?>
    </div>
</div>