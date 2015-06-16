<?php
/**
 * Отображение страницы поиска по разделу
 */
/* @var $this CatalogData */
?>
<div class="row-fluid">
    <div class="span9" id="search_results">
        <?php 
        // при первой загрузке страницы попробуем получить данные поиска из сессии
        $data = CatalogModule::getSessionSearchData('filter', $this->section->id);
        // отображение найденных анкет
        $this->widget('catalog.extensions.search.QSearchResults.QSearchResults', array(
            'mode'         => 'filter',
            'searchObject' => $this->section,
            'data'         => $data,
        ));
        ?>
    </div>
    <div class="span3">
        <?php 
        // форма поиска
        $this->widget('catalog.extensions.search.SearchFilters.SearchFilters', array(
            'section'    => $this->section,
            'dataSource' => 'session',
        ));
        ?>
    </div>
</div>