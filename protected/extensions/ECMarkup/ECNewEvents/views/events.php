<?php
/**
 * Список событий
 */
/* @var $this ECNewEvents */
?>
</div>
</div>

<div class="ec-wrapper">
<div id="ec-content">
<?php 
$this->widget('ext.ECMarkup.ECObjectSlider.ECObjectSlider', array(
    'objects'     => $events,
    'includeJs'   => false,
    'containerId' => 'slider_news',
    'prevId'      => 'previousEvents',
    'nextId'      => 'nextEvents',
    'options'     => array(
        'height' => 250,
        'width'  => 1030,
        'auto'   => false,
    ),
));
?>
</div>
</div>

<div class="container" id="page">
<div id="content">