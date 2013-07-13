<?php

$image = '';
// Проверка на случай если нет картинки проекта
if ( $cover = $data->getAvatarUrl() )
{
    $image = $cover;
}
?>
<li class="span2">
    <a href="<?= Yii::app()->createUrl('/projects/projects/view', array('id' => $data->id));?>" id="catalog-thumbnail-<?= $data->id; ?>" class="thumbnail" 
        rel="tooltip" data-title="<?= implode(', ', array($data->name, $data->typetext)); ?>">
        <img style="border-radius: 10px; width: 150px;height: 150px;" src="<?= $image; ?>" alt="<?= CHtml::encode($data->name); ?>">
    </a>
</li>