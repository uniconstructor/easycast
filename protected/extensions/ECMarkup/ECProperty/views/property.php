<?php
/**
 * Разметка для отображения одного свойства
 */
/* @var $this ECProperty */
?>
<?= CHtml::openTag('div', $this->htmlOptions); ?>
    <div class="ec-property-caption"><?= $this->label; ?></div>
    <?= CHtml::openTag($this->valueTag, $this->valueOptions); ?>
        <?= $this->value; ?><small class="muted"><?= $this->affix; ?></small>
    <?= CHtml::closeTag($this->valueTag); ?>
    <small class="muted"><?= $this->hint; ?></small>
<?= CHtml::closeTag('div'); ?>