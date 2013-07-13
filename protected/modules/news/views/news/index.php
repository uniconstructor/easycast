<?php
/**
 * Список всех новостей
 */

$this->breadcrumbs=array(
    NewsModule::t('news'),
);

?>
<div class="row span12">
    <h1><?php echo NewsModule::t('news'); ?></h1>
    
    <?php 
    // Виджет с основными заголовками
    
    
    // Сам список новостей
    $this->widget('news.extensions.NewsList.NewsList');
    ?>
</div>