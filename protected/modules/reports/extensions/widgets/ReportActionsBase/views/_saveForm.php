<?php
/**
 * Форма со стандартными действиями для заказа
 * 
 * @var ReportActionsBase $this
 * @var TbActiveForm $form
 * 
 * @todo языковые строки
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'                   => 'report-form',
    'enableAjaxValidation' => false,
));

foreach ( $this->saveParams as $name => $value )
{
    echo CHtml::hiddenField('options['.$name.']', $value);
}
echo  $form->errorSummary($this->report);

// Название отчета
echo $form->textFieldRow($this->report, 'name', array('maxlength' => 255, 'style' => 'width:100%;'));

// RichText редактор для комментария к отчету
echo $form->labelEx($this->report, 'comment');
$this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    'model'     => $this->report,
    'attribute' => 'comment',
    'options'   => array('lang' => 'ru'),
));
echo $form->error($this->report, 'comment');
echo '<br>';

// Кнопка сохранения отчета
if ( $this->allowSave )
{
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type'       => 'primary',
        'size'       => 'large',
        'label'      => 'Создать',
        'htmlOptions' => array(
            'id' => 'save_report',
        ),
    ));
}
$this->endWidget(); 
