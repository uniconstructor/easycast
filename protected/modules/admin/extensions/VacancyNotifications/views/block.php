<?php
/**
 * Блок с текстовым полем для редактирования фрагмента письма
 */
/* @var $this      VacancyNotifications */
/* @var $form      TbActiveForm */
/* @var $blockItem EasyListItem */

// скрипт показа формы вставки элемента
$showFormJs = "function (data, status) {
    $('#config_split_block_after_{$blockItem->id}').show();
    $('#showBlockButton_{$blockItem->id}').show();
    
}";
?>
<div class="row-fluid" id="config_notify_block_<?= $blockItem->id; ?>">
    <div class="offset2 span8">
        <?php
        // форма редактирования блока
        $this->printBlockForm($blockItem, true);
        ?>
        <div class="well text-center" id="showBlockButton_<?= $blockItem->id; ?>">
            <?php 
            // вставка нового блока в это место
            echo CHtml::link('Вставить блок', '#', array(
                //'id'    => 'showBlockButton_'.$blockItem->id,
                'class' => 'btn btn-success',
                'click' => $showFormJs,
            ));
            ?>
        </div>
    </div>
</div>
<div class="row-fluid" id="config_split_block_after_<?= $blockItem->id; ?>" style="display:none;">
    <div class="offset2 span8">
        <?php
        $insertBlockUrl = Yii::app()->createUrl($this->createUrl, array('afterItemId' => $blockItem->id));
        // скрытая пустая форма для вставки элемента между блоками
        $this->printBlockForm($blockItem, true);
        ?>
    </div>
</div>