<?php
/**
 * Форма создания вакансии (роли)
 */
/* @var $this  EventVacancyController */
/* @var $model EventVacancy */
/* @var $form  TbActiveForm */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'     => 'event-vacancy-form',
    'action' => Yii::app()->createUrl('//admin/eventVacancy/update', array(
        'id'   => $model->id,
        'step' => 'Search',
    )),
    'enableAjaxValidation'   => true,
    'enableClientValidation' => false,
    'clientOptions' => array(
        'validateOnSubmit' => false,
        'validateOnChange' => true,
        /*'afterValidate' => "js:function(form, data, hasError) {
            console.log('afterValidate');
            return true;
        }",*/
    ),
));

// название роли
echo $form->textFieldRow($model, 'name', array('maxlength' => 255));
// описание роли
echo $form->redactorRow($model, 'description');
// необходимое количество человек
echo $form->textFieldRow($model, 'limit', array('maxlength' => 6));
// оплата за день
echo $form->textFieldRow($model, 'salary', array('maxlength' => 7));

// ошибки при заполнении
//echo $form->errorSummary($model);
echo $form->errorSummary($model, null, null, array('id' => 'event-vacancy-form-es'));

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'ajaxSubmit',
    'type'       => 'primary',
    'size'       => 'large',
    'label'      => 'Сохранить',
    'htmlOptions' => array(
        'style' => 'display:none;'
    ),     
));

$this->endWidget();