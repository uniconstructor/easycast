<?php
/**
 * Форма со стандартными действиями для заказа
 * 
 * @var ReportActionsBase $this
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'report-form',
    'enableAjaxValidation' => false,
));

foreach ( $this->saveParams as $name=>$value )
{
    echo CHtml::hiddenField('options['.$name.']', $value);
}
?>
    <?php echo $form->errorSummary($this->report); ?>
    <?php echo $form->textFieldRow($this->report, 'name', array('maxlength'=>255)); ?>
    <br>
    <?php 
    if ( $this->allowSave )
    {
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'type'       => 'primary',
            'size'       => 'large',
            'label'      => Yii::t('coreMessages', 'save'),
            'htmlOptions' => array(
                'id' => 'save_report',
            ),
        ));
    }
?>
<?php $this->endWidget(); ?>
