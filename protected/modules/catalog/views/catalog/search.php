<?php
/**
 * Большая форма поиска, в которой есть все
 * Форма не использует модель
 */
?>
<h1>Поиск</h1>

<div class="row">
    <div class="span9" id="search_results" style="margin-top:10px;">
        <?php
        // Выводим виджет поисковых результатов
        // при первой загрузке страницы попробуем получить данные поиска из сессии
        $data = CatalogModule::getSessionSearchData('form');
        $options = array(
            'mode' => 'search',
            'data' => $data,
        );
        $this->widget('catalog.extensions.search.QSearchResults.QSearchResults', $options);
        ?>
    </div>
    <div class="span3">
        <?php
        // форма поиска
        // поиск по разделу "вся база", поэтому получаем первый раздел (корень дерева)
        $section = CatalogSection::model()->findByPk(1);
        $this->widget('catalog.extensions.search.QSearchFilters.QSearchFilters', array(
            'mode'    => 'form',
            'section' => $section,
        ));
        ?>
    </div>
</div>
