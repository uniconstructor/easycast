<?php
/**
 * Большая форма поиска, в которой есть все
 * Форма не использует модель
 */
?>
<h1>Поиск</h1>
<div class="span12">
<?php
    // Выводим виджет большой формы поиска 
    $this->widget('catalog.extensions.search.QSearchForm.QSearchForm');
?>
</div>

<div class="span12" id="search_results" style="margin-top:10px;">
    <?php
    // Выводим виджет поисковых результатов
    // при первой загрузке страницы попробуем получить данные поиска из сессии
    $data = CatalogModule::getSessionSearchData();
    if ( isset($data['form']) )
    {
        $data = $data['form'];
    }else
    {
        $data = array();
    }
    $options = array(
        'mode' => 'search',
        'data' => $data,
    );
    $this->widget('catalog.extensions.search.QSearchResults.QSearchResults', $options);
    ?>
</div>

