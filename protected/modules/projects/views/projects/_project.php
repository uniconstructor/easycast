<?php
/**
 * Шаблон отображения одного проекта
 */
/* @var $this ProjectsController */
/* @var $data Project */

// Проверка на случай если нет картинки проекта
if ( $coverUrl = $data->getAvatarUrl() )
{
    $image = '<img style="border-radius:10px;width:150px;height:150px;" src="'.$coverUrl.'" alt="'.CHtml::encode($data->name).'">';
}else
{
    $image = '<div style="text-align:center;border-radius:10px;width:150px;height:150px;"><br><br><h4 style="margin:0px;padding:0px;">'.$data->name.'</h4></div>';
}
?>
<li class="span2">
    <a href="<?= Yii::app()->createUrl('/projects/projects/view', array('id' => $data->id));?>" id="catalog-thumbnail-<?= $data->id; ?>" class="thumbnail" 
        rel="tooltip" data-title="<?= implode(', ', array($data->name, $data->typetext)); ?>">
        <?= $image; ?>
    </a>
</li>