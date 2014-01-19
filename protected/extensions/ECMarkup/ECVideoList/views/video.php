<?php
/**
 * Разметка shadowbox для добавления видео
 * 
 * @todo добавить остальные типы видео
 */
/* @var $this ECVideoList */
/* @var $data Video */


$linkOptions = array();
$image = CHtml::image($data->getPreviewUrl(), CHtml::encode($data->name), array(
    'style' => 'border-radius:5px;height:90px;width:120px;')
);

if ( $data->type == 'youtube' AND $data->externalid )
{
    $url = Sweeml::raiseOpenShadowboxUrl($data->getEmbedUrl(), $this->shadowBox);
}else
{
    $url = Sweeml::raiseOpenShadowboxUrl('#', array(
        'player'  => 'html',
        'content' => '<div style="background-color:#ffffff;text-align:center;height:240px;width:360px;"><br><br><br><br><br>'.
            CHtml::link('<h4>(Открыть видео в новом окне)</h4>', $data->link, array('target' => '_blank')).'</div>',
        'width'   => 360,
        'height'  => 240,
    ));
}

?>
<li class="span4">
    <?= Sweeml::link($image, $url, array('class' => "thumbnail")); ?>
    <?= Sweeml::link(CHtml::encode($data->name), $url); ?>
</li>

