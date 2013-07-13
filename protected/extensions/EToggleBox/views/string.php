<?php
/**
 * Display element as long string in form
 */
?>
<table style="vertical-align:bottom; width:95%;">
<tr>
    <td style="width:35%;">
        <?= CHtml::activeLabelEx($this->model, $this->attribute); ?>
    </td>
    <td>
        <div>
            <?= CHtml::activeCheckBox($this->model, $this->attribute, array('display' => 'none')); ?>
        </div>
    </td>
</tr>
</table>