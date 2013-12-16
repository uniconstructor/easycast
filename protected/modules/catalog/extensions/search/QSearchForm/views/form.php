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
    <div class="ec-usual_suspects span11"></div>
    <div class="row span12">
        <div class="search-column span3">
        <ul>
            <li>
                <h3>Основная информация</h3>
            </li>
            <?php
            // основные фильтры
            $this->displayColumnFilters('base');
            ?>
        </ul>
        </div>
        <div class="search-column span3">
        <ul>
            <li>
                <h3>Внешность</h3>
            </li>
            <?php
            // фильтры по внешности
            $this->displayColumnFilters('looks');
            ?>
        </ul>
        </div>
        <div class="search-column span3">
        <ul>
            <li>
                <h3>Навыки</h3>
            </li>
            <?php
            // фильтры по навыкам
            $this->displayColumnFilters('skills');
            ?>
        </ul>
        </div>
        <div class="search-column span2"><!-- style="margin-top: 50px; text-align: center;" -->
            <ul>
                <li>
                    <h3>&nbsp;</h3>
                </li>
                <li>
                    <?php 
                    // Добавляем кнопки "очистить" и "найти"
                    $this->displayButtons();
                    ?>
                </li>
            </ul>
        </div>
    </div>
</div>