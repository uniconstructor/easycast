<?php 
/**
 * Форма создания вакансии (роли)
 */

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
$this->widget('bootstrap.widgets.TbButton', array(
	'buttonType' => 'submit',
	'type'       => 'primary',
	'size'       => 'large',
	'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить',
)); 

$this->endWidget();