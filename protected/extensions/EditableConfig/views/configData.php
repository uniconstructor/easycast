<?php
/**
 * Верстка одной настройки из списка
 * 
 * @todo информация об изменениях
 * @todo связянные объекты
 * @todo чем используется
 * @todo совпадает ли со стандартной
 */
/* @var $this ConfigData */

?>
<div class="well">
    <div class="row-fluid">
        <div class="span9">
            <?php 
            // содержимое настройки
            $this->widget('bootstrap.widgets.TbBox', array(
                'title'      => $config->title,
                'headerIcon' => 'icon-cogs',
                'content'    => $content,
            ));
            ?>
        </div>
        <div class="span3">
            <div>
                <div class="popover right fade in" style="display:block;position:relative;">
                    <div class="arrow"></div>
                    <h3 class="popover-title muted">[<?= $config->name; ?>]</h3>
                    <div class="popover-content">
                        <p><?= $config->description; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>