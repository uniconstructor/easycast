<?php
/**
 * Форма со стандартными действиями для заказа
 * 
 * @var CallListActions $this
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

// галочки для выбора статусов заявок, которые будут включены в фотовызывной
echo CHtml::label('Статусы заявок', 'lang_0');
echo CHtml::checkBoxList('statuses', array('active'), array(
        ProjectMember::STATUS_ACTIVE  => 'Утвержденные',
        ProjectMember::STATUS_PENDING => 'Предварительно одобренные',
        ProjectMember::STATUS_DRAFT   => 'На рассмотрении',
    ),
    array(
        'labelOptions' => array('style' => 'display:inline;'),
    )
);

// на каком языке сформировать фотовызывной?
echo CHtml::label('Язык', 'lang_0');
echo CHtml::radioButtonList('lang', 'ru', array(
        'ru' => 'Русский',
        'en' => 'Английский',
    ),
    array(
        'labelOptions' => array('style' => 'display:inline;'),
    )
);
echo '<br>';

if ( $this->allowSave )
{// Кнопка сохранения отчета
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType'  => 'submit',
        'type'        => 'primary',
        'size'        => 'large',
        'label'       => 'Создать',
        'htmlOptions' => array(
            'id' => 'save_report',
        ),
    ));
}
$this->endWidget(); 
