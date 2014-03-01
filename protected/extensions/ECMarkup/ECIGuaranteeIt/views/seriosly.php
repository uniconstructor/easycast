<?php
/**
 * Верстка страницы с текстом гарантий.
 * Серьезно.
 */
/* @var $this ECIguaranteeIt */
?>
<!--div style="position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	overflow: auto;
	overflow-y: auto;"-->
    <?
    // начало всплывающего окна
    $this->beginWidget('bootstrap.widgets.TbModal', array(
        'id'          => $this->modalId,
        'htmlOptions' => array(
            'style' => 'width:80%;left:30%;top:1%;bottom:1%;',
        ),
    ));
    ?>
    <div class="modal-header">
        <a class="close white" data-dismiss="modal">&times;</a>
        <h3 style="text-align: center;">Почему именно мы?</h3>
    </div>
    <div class="modal-body" style="text-align:center;max-height: 85%;">
        <img style="max-width:1000px;" src="<?= $this->assetUrl.'/fulltext.jpg' ?>">
    </div>
    <div class="modal-footer">
        <?php
        // закрыть
        $this->widget('bootstrap.widgets.TbButton', array(
            'label'       => 'Закрыть',
            'htmlOptions' => array('data-dismiss' => 'modal'),
            'type'        => 'success',
        )); 
        ?>
    </div>
    <?php 
    // конец всплывающего окна
    $this->endWidget($this->modalId);
    ?>
<!--/div-->