<?php
/**
 * Нижняя часть списка заявок на участие в проекте с кнопкой "завершить отбор"
 */
/* @var $this TokenSelection */
?>

<div style="text-align:center;">
<?php 
$this->widget('bootstrap.widgets.TbButton',
    array(
        'buttonType' => 'link',
        'type'  => 'success',
        'size'  => 'large',
        'label' => 'Завершить отбор',
        'url'   => Yii::app()->createUrl('/projects/invite/finishSelection', $linkParams),
        'htmlOptions' => array(
            'id' => 'finish_selection',
            'confirm' => 'Завершить отбор участников?',
        )
    )
);
?>
</div>