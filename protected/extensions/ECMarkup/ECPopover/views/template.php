<?php
/**
 * Измененный шаблон popover-подсказки (чтобы можно было задавать ширину и другие свойства)
 */
/* @var $this ECPopover */

echo CHtml::openTag('div', $this->htmlOptions);
?>
<div class="arrow"></div>
<div class="popover-inner">
    <h3 class="popover-title" id="<?= $this->titleId; ?>" style="font-weight:bold;"></h3>
    <div class="popover-content">
        <p id="<?= $this->contentId; ?>">&nbsp;</p>
    </div>
</div>
<?php 
echo CHtml::closeTag('div');
?>