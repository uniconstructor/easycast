<?php
/**
 * Разметка одного блока со стрелкой
 */
/* @var $this ECKeyValue */

$keyBgClass   = 'ec-key-bg-'.$this->type;
$valueBgClass = 'ec-value-bg-'.$this->type;

echo CHtml::openTag('div', $this->htmlOptions);
?>
    <div class="ec-key-block  ec-key-block-<?= $this->type; ?> <?= $keyBgClass; ?>">
        <?= $this->label; ?>
    </div>
    <svg class="ec-triangle-spacer ec-triangle-spacer-<?= $this->type; ?> <?= $valueBgClass; ?>" 
        preserveAspectRatio="none" viewBox="0 0 100 102" height="<?= $this->spacerHeight; ?>" width="100%" 
        version="1.1" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 0 L50 100 L100 0 Z">
    </svg>
    <div class="ec-value-block ec-value-block-<?= $this->type; ?> <?= $valueBgClass; ?>">
        <?= $this->value; ?>
    </div>
<?php
echo CHtml::closeTag('div');