<?php
/**
 * Верстка одной настройки из списка
 * 
 * @todo информация об изменениях
 * @todo связянные объекты
 * @todo чем используется
 * @todo совпадает ли со стандартной
 */
/* @var $this DefaultConfigData */
?>
<div class="well">
    <div class="row-fluid">
        <div class="span9">
            <blockquote>
                <p><?= $this->config->title; ?></p>
                <small>[<?= $this->config->name; ?>]</small>
            </blockquote>
            
        </div>
        <div class="span3">
            <div>
                <div class="popover right fade in" style="display:block;position:relative;">
                    <div class="arrow"></div>
                    <!--h3 class="popover-title"><?= $this->getConfigType(); ?></h3-->
                    <div class="popover-content">
                        <p><?= $this->config->description; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>