<?php
/**
 * Кнопки в одну строку
 */
/* @var $this QSearchFilterIconList */
/* @var $sections CatalogSection[] */
?>
<div class="row-fluid text-center">
    <h4 class="intro-description muted" style="margin-bottom:5px;">Разделы каталога</h4>
    <?php 
    for ( $i = 0; $i < count($sections); $i++ )
    {// располагаем кнопки по 6 элементов в строке
        $activeButtonClass = '';
        $checkedValue      = '';
        $section           = $sections[$i];
        if ( isset($data['sections']) AND in_array($section->id, $data['sections']) )
        {
            $activeButtonClass = 'btn-primary';
            $checkedValue      = 'checked="checked"';
        }
        // выводим кнопку раздела
        $this->render('_button', array(
            'section'           => $section,
            'activeButtonClass' => $activeButtonClass,
            'checkedValue'      => $checkedValue,
        ));
    }
    ?>
</div>
