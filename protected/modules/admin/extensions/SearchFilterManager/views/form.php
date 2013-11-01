<?php
/**
 * @package easycast
 * @var TbActiveForm $form
 */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'catalog-filters-form',
    'enableAjaxValidation' => false,
    'method' => 'post',
));
?>
<div class="span6">
    <div class="row">
        <div class="span3">
            <h3>Выбрать</h3>
            <?php
            $this->printSortableList('pending');
            ?>
        </div>
        <div class="span3">
            <h3>Уже добавлено</h3>
            <?php
                $this->printSortableList('active');
            ?>
        </div>
    </div>
    <div class="span4" style="text-align: center;">
    <?php 
    echo CHtml::hiddenField('activeFilters', '', array('id' => 'activeFilters'));
    $form->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type'  => 'success',
        'size'  => 'large',
        'label' => 'Сохранить критерии поиска',
        'ajaxOptions' => array(
            'method' => 'post',
        ),
        //'beforeSend' => $beforeSendJS,
    ));
    ?>
    <?php $this->endWidget(); ?>
    </div>
</div>
