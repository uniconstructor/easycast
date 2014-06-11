<?php
/**
 * Страница формы поиска в разделе каталога или по всей базе
 */
/* @var $this CatalogController */

$this->breadcrumbs = array(
    'Поиск',
);
?>
<div class="page-alternate">
    <div class="row-fluid">
        <div class="container">
                <?php
                // верхняя полоска со списком разделов каталога
                // получаем корневой раздел каталога ("вся база") для того чтобы искать по всем доступным анкетам
                $rootSection = CatalogSection::model()->findByPk(1);
                // виджет расширенной формы поиска (по всей базе)
                $this->widget('catalog.extensions.search.QSectionHelper.QSectionHelper', array(
                    'searchObject' => $rootSection,
                ));
                ?>
        </div>
        <div class="row-fluid">
            <div class="span9" id="search_results" style="margin-top:10px;">
                <?php
                // Выводим виджет поисковых результатов
                // при первой загрузке страницы попробуем получить данные поиска из сессии
                $data        = CatalogModule::getSessionSearchData('filter', 1);
                $rootSection = CatalogSection::model()->findByPk(1);
                $options = array(
                    'mode'         => 'filter',
                    'data'         => $data,
                    'searchObject' => $rootSection,
                );
                $this->widget('catalog.extensions.search.QSearchResults.QSearchResults', $options);
                ?>
            </div>
            <div class="span3">
                <?php
                // форма поиска (поиск по разделу "вся база")
                $this->widget('catalog.extensions.search.SearchFilters.SearchFilters', array(
                    'mode'          => 'filter',
                    'backToFormUrl' => '/search',
                    'searchObject'  => $rootSection,
                    'dataSource'    => 'session',
                ));
                ?>
            </div>
        </div>
    </div>
</div>