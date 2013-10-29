<?php
/**
 * 
 */



?>
<div class="span6">
    <div class="row">
        <div class="span3">
            <h3>Доступные критерии</h3>
            <?php
                $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                    'id' => 'catalog-filters-form',
                    'enableAjaxValidation' => false,
                ));
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
        $form->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type'  => 'success',
            'size'  => 'large',
            'label' => 'Сохранить критерии поиска',
        ));
    ?>
    <?php $this->endWidget(); ?>
    </div>
</div>
