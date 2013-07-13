<?php

/**
 * @var $this CWidget
 */

/**
 * Разметка для формы поиска
 * Определяет как и в каком порядке располагаются поля в форме. 
 * Один критерий поиска - один виджет
 * 
 * Форме разделена на 3 колонки
 */
?>
<div class="span4">
<?php // Пол
    $options = array(
        'display' => 'form',
        'filter'  => CatalogFilter::model()->find("`shortname` = 'gender'"),
    );
    $this->widget('catalog.extensions.search.filters.QSearchFilterGender.QSearchFilterGender', $options);
?>
<?php // Возраст
    $options = array(
        'display' => 'form',
        'filter'  => CatalogFilter::model()->find("`shortname` = 'age'"),
    );
    $this->widget('catalog.extensions.search.filters.QSearchFilterAge.QSearchFilterAge', $options);
?>
<?php // Рост
    $options = array(
        'display' => 'form',
        'filter'  => CatalogFilter::model()->find("`shortname` = 'height'"),
    );
    $this->widget('catalog.extensions.search.filters.QSearchFilterHeight.QSearchFilterHeight', $options);
?>
<?php // Вес
    $options = array(
        'display' => 'form',
        'filter'  => CatalogFilter::model()->find("`shortname` = 'weight'"),
    );
    $this->widget('catalog.extensions.search.filters.QSearchFilterWeight.QSearchFilterWeight', $options);
?>
</div>
<div class="span4"></div>
<div class="span4"></div>
<div class="span12 text-center">
    <?php 
        // Добавляем внизу кнопки "очистить" и "найти"
        $this->displayButtons();
    ?>
</div>