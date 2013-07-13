<?php
/**
 * Отображает одну картинку пользователя
 *
 * @var data $data
 */

$url   = Yii::app()->createUrl('catalog/catalog/index', array('sectionid' => $data->id));
$image = $data->getAvatarUrl();
?>


<li class="span2">
<div class="carousel-inner">
    <div class="carousel-slide">
        <div class="item active">
            <a href="<?= $url; ?>" id="catalog-thumbnail-<?= $data->id; ?>" 
                class="thumbnail" rel="tooltip" data-title="<?= $data->name; ?>">
                <img style="border-radius: 10px; width: 150px; height: 150px;" src="<?= $image; ?>" alt="<?= $data->name; ?>">
            </a>
            <div class="ec-catalog-section-caption carousel-caption">
                <p class="ec-catalog-section-title"><a href="<?= $url; ?>"><?= $data->name; ?></a></p>
            </div>
        </div>
    </div>
</div>
</li>