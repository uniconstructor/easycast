<?php
/**
 * Просмотр одной новости
 */
/**
 * @var News $newsItem
 */

$this->breadcrumbs=array(
    NewsModule::t('news') => '/news',
    CHtml::encode($newsItem->name),
);

?>
<div class="row span 12">
    <h1><?= CHtml::encode($newsItem->name); ?></h1>
    <p><?= $newsItem->content; ?></p>
</div>