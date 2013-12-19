<?php
/**
 * Разметка для краткой формы поиска на главной странице
 * Определяет как и в каком порядке располагаются поля в форме.
 * Один критерий поиска - один виджет
 *
 * Форма разделена на 4 колонки
 */
/* @var $this QSearchForm */

?>
<div class="ec-advanced-search">
    <?php 
    // верхняя полоска со списком разделов
    $filter = CatalogFilter::model()->find("`shortname` = 'iconlist'");
    $this->widget('catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass, array(
        'display'      => 'form',
        'filter'       => $filter,
        'searchObject' => $this->searchObject,
        'refreshDataOnChange' => false,
    ));
    ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid"> 
            <div class="span3">
            <ul>
                
                <?php
                // основные фильтры
                $this->displayColumnFilters('base');
                ?>
            </ul>
            </div>
            <div class="span3">
            <ul>
                
                <?php
                // фильтры по внешности
                $this->displayColumnFilters('looks');
                ?>
            </ul>
            </div>
            <div class="span3">
            <ul>
                
                <?php
                // фильтры по навыкам
                $this->displayColumnFilters('skills');
                ?>
            </ul>
            </div>
            <div class="search-column span3"><!-- style="margin-top: 50px; text-align: center;" -->
                <ul>
                    
                    <li>
                        <?php 
                        // добавляем кнопки "очистить" и "найти"
                        $this->displayButtons();
                        ?>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>