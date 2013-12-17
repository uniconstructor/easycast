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
<div class="ec-advanced-search">
    <?php 
    // верхняя полоска со списком разделов
    $filter = CatalogFilter::model()->find("`shortname` = 'iconlist'");
    $this->widget('catalog.extensions.search.filters.'.$filter->widgetclass.'.'.$filter->widgetclass, array(
        'display'      => 'form',
        'filter'       => $filter,
        'searchObject' => $this->searchObject,
        'refreshDataOnChange' => $this->refreshDataOnChange,
    ));
    ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="row-fluid"> 
            <div class="span3">
            <ul>
                <li>
                    <h3 style="color:#497A89;font-size:20px;font-weight:normal;">Основная информация</h3>
                </li>
                <?php
                // основные фильтры
                $this->displayColumnFilters('base');
                ?>
            </ul>
            </div>
            <div class="span3">
            <ul>
                <li>
                    <h3 style="color:#497A89;font-size:20px;font-weight:normal;">Внешность</h3>
                </li>
                <?php
                // фильтры по внешности
                $this->displayColumnFilters('looks');
                ?>
            </ul>
            </div>
            <div class="span3">
            <ul>
                <li>
                    <h3 style="color:#497A89;font-size:20px;font-weight:normal;">Навыки</h3>
                </li>
                <?php
                // фильтры по навыкам
                $this->displayColumnFilters('skills');
                ?>
            </ul>
            </div>
            <div class="search-column span3"><!-- style="margin-top: 50px; text-align: center;" -->
                <ul>
                    <li>
                        <h3>&nbsp;</h3>
                    </li>
                    <li>
                        <?php 
                        // добавляем кнопки "очистить" и "найти"
                        $this->displayButtons();
                        ?>
                    </li>
                    <li style="display:block;">
                        <?php 
                        // добавляем счетчик подходищих участников (если нужно)
                        if ( $this->countUrl or true)
                        {
                            $this->render('_count');
                        } 
                        ?>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
</div>