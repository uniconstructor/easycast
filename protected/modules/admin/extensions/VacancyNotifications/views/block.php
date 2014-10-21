<?php
/**
 * Блок с текстовым полем для редактирования фрагмента письма
 */
/* @var $this      VacancyNotifications */
/* @var $form      TbActiveForm */
/* @var $blockItem EasyListItem */

// скрипт показа формы вставки элемента
$showFormJs = "
    $('#showBlockButton_{$blockItem->id}').hide();
    $('#config_split_block_after_{$blockItem->id}').show();
    return false;
";
?>
<div class="row-fluid" id="config_notify_block_<?= $blockItem->id; ?>">
    <div class="offset2 span8">
        <?php
        // форма редактирования блока
        $this->printBlockForm($blockItem);
        ?>
        <div class="well text-center" id="showBlockButton_<?= $blockItem->id; ?>">
            <?php 
            // вставка нового блока в это место
            echo CHtml::link('Вставить блок', '#', array(
                'class'   => 'btn btn-success',
                'onclick' => $showFormJs,
            ));
            ?>
        </div>
    </div>
</div>
<div class="row-fluid" id="config_split_block_after_<?= $blockItem->id; ?>" style="display:none;">
    <div class="offset2 span8">
        <?php
        // скрытая пустая форма для вставки элемента между блоками
        $this->printBlockForm($blockItem, true);
        ?>
    </div>
</div>