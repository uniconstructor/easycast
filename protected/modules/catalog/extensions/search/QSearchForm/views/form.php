<?php
/**
 * Разметка для формы поиска
 * Определяет как и в каком порядке располагаются поля в форме.
 * Один критерий поиска - один виджет
 *
 * Форма разделена на 4 колонки
 */
/* @var $this QSearchForm */

?>
<div class="row-fluid">
    <div class="row-fluid text-center">
        <?php 
        // добавляем счетчик подходящих участников (если нужно)
        if ( $this->countUrl or true)
        {
            $this->render('_count');
        } 
        ?>
    </div>
    <!-- 4 колонки с критериями поиска -->
    <div class="row-fluid"> 
        <div class="span3">
            <h3 style="color:#497A89;font-size:20px;font-weight:normal;">Разделы каталога</h3>
            <?php
            // список разделов каталога, отображаемый вертикально
            $filter = CatalogFilter::model()->find("`shortname` = 'iconlist'");
            $this->widget('catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass, array(
                'display'             => 'form',
                'filter'              => $filter,
                'searchObject'        => $this->searchObject,
                'refreshDataOnChange' => $this->refreshDataOnChange,
                'buttonAlignment'     => 'vertical',
            ));
            ?>
        </div>
        <div class="span3">
            <h3 style="color:#497A89;font-size:20px;font-weight:normal;">Основная информация</h3>
            <?php
            // основные фильтры
            $this->displayColumnFilters('base');
            ?>
        </div>
        <div class="span3">
            <h3 style="color:#497A89;font-size:20px;font-weight:normal;">Внешность</h3>
            <?php
            // фильтры для внешности
            $this->displayColumnFilters('looks');
            ?>
        </div>
        <div class="span3">
            <h3 style="color:#497A89;font-size:20px;font-weight:normal;">Навыки</h3>
            <?php
            // фильтры по навыкам
            $this->displayColumnFilters('skills');
            ?>
        </div>
    </div>
    <div class="row-fluid text-center">
    <?php 
    // добавляем кнопки "очистить" и "найти"
    $this->displayButtons();
    ?>
    </div>
</div>