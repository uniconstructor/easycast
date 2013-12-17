<?php
/**
 * Полоска иконок со списком разделов каталога
 */
/* @var $this QSearchSections */
/* @var $sections CatalogSection[] */
?>

<div class="ec-usual_suspects span12">
    <?php 
    foreach ( $sections as $section )
    {// рисуем по одной кнопке для каждого раздела
        $this->render('_button', array('section' => $section));
    }
    ?>
</div>