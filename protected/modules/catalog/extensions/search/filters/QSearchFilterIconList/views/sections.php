<?php
/**
 * Полоска иконок со списком разделов каталога
 */
/* @var $this QSearchSections */
/* @var $sections CatalogSection[] */
?>

<div class="ec-usual_suspects span12">
    <?php 
    // вспоминаем, какие разделы были выбраны в последний раз
    $data = $this->loadLastSearchParams();
    foreach ( $sections as $section )
    {// рисуем по одной кнопке для каждого раздела
        $activeButtonClass = '';
        $checkedValue      = '';
        if ( in_array($section->id, $data['sections']) )
        {
            $activeButtonClass = 'ec-search-section-active';
            $checkedValue      = 'checked="checked"';
        }
        $this->render('_button', array(
            'section'           => $section,
            'activeButtonClass' => $activeButtonClass,
            'checkedValue'      => $checkedValue,
        ));
    }
    ?>
</div>