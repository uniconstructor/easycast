<?php
/**
 * Изображение со ссылкой на внешнюю страницу
 */
/* @var $this EMailSegment */

$image = $this->render('misc/_image', array(
    'link'  => $this->imageLink,
    'style' => $this->imageStyle,
), true);

echo CHtml::link($image, $this->imageTarget, array('target' => '_blank'));