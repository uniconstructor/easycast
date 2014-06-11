<?php
/**
 * Полоска со списком разделов каталога
 */
/* @var $this QSectionHelper */
?>
<div class="ec-advanced-search">
    <?php
    // верхняя полоска со списком разделов
    $filter = CatalogFilter::model()->find("`shortname` = 'iconlist'");
    $this->widget('catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass, array(
        'display'             => 'form',
        'dataSource'          => $this->dataSource,
        'filter'              => $filter,
        'searchObject'        => $this->searchObject,
        // обновляем результаты поиска каждый раз при изменении списка разделов
        'refreshDataOnChange' => true,
    ));
    ?>
</div>