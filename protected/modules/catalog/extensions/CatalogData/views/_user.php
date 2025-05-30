<?php
/**
 * Отображает одну анкету участника в поиске
 */
/* @var $this CatalogData */
/* @var $data Questionary */

$image = $data->getAvatarUrl();
if ( $cover = $data->getAvatarUrl('catalog') )
{// Проверка на случай если пользователь не загрузил изображение
    $image = $cover;
}

if ( $data and isset($data->user) )
{
?>
<li>
    <a href="<?= Yii::app()->createUrl(Yii::app()->getModule('questionary')->profileUrl, array('id' => $data->id));?>" 
        id="catalog-thumbnail-<?= $data->id; ?>" class="thumbnail" target="_blank"
        rel="tooltip" data-title="<?= implode(', ', array($data->user->fullname, $data->age)); ?>">
        <img style="border-radius: 10px;width: 150px;height: 150px;" src="<?= $image; ?>" alt="<?= $data->user->fullname; ?>">
    </a>
</li>
<?php 
}
?>