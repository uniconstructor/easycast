<?php
/**
 * Форма создания вакансии (роли)
 */
/* @var $this  EventVacancyController */
/* @var $model EventVacancy */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id' => 'event-vacancy-form',
    'enableAjaxValidation' => false,
));

// название роли
echo $form->textFieldRow($model, 'name', array('maxlength' => 255));
// описание роли
echo $form->redactorRow($model, 'description');
// необходимое количество человек
echo $form->textFieldRow($model, 'limit', array('maxlength' => 6));
// оплата за день
echo $form->textFieldRow($model, 'salary', array('maxlength' => 7));

echo '<br>';
$form->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type'       => 'primary',
    'size'       => 'large',
    'label'      => 'Начать',
    'htmlOptions' => array(
        'name'  => 'submit',
    ),
));
$form->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type'       => 'primary',
    'size'       => 'large',
    'label'      => 'Начать',
    'htmlOptions' => array(
        'name'  => 'submit',
    ),
));

$this->endWidget();