<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditRequiredFields */
/* @var $model QFieldInstance */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));

// тип и id для привязки нового поля 
echo CHtml::hiddenField('objectType', $this->bindObjectType);
echo CHtml::hiddenField('objectId', $this->objectId);

//echo CHtml::label('Шаг регистрации', 'stepid');
//echo CHtml::dropDownList('stepid', $this->getFieldStep($model), $this->getWizardSteps());
// шаг регистрации (если в роли используется регистрация по шагам)
if ( $this->bindObjectType === 'wizardstepinstance' )
{
    echo $form->dropDownListRow($model, 'objectid', $this->getWizardStepOptions(), array(), array());
}
// прикрепляемое поле
echo $form->dropDownListRow($model, 'fieldid', $this->getFieldIdOptions(), array(), array());
// галочка "обязательно к заполнению"
echo $form->dropDownListRow($model, 'filling', $model->getFillingModes(), array(), array(
    'hint' => '<small><b>Да:</b> без заполнения этого поля будет нельзя подать заявку<br>
        <b>Нет:</b> без этого поля можно будет подать заявку 
        (но если в анкете участника оно пустое - все равно предложим заполнить)<br>
        <b>Заполнено автоматически:</b> поле нельзя будет редактировать участнику, его значение будет выставлено
        автоматически, в форме его видно не будет. Полезно для автоматического заполнения значений
        (например всем подавшим заявку на топ-модель надо автоматически проставить женский пол и галочку "модель").
        </small>',
));
// текстовое поле
echo $form->textFieldRow($model, 'data', array(), array(
    'hint' => '<small>Используется только вместе с "заполнено автоматически".<br>
        Допустимые значения для галочек:<br>
        0: "нет"<br>1: "да"<br>
        Для поля "пол":<br>
        "male" - мужской<br>"female" - женский</small>',
));


// @todo вернутся к этому элементу когда у него будет добавлена возможность выбирать более 2 вариантов
/*echo $form->widgetRow('ext.ECMarkup.ECToggleInput.ECToggleInput', 
    array(
        'model'     => $model,
        'attribute' => 'filling',
        'onLabel'   => 'Да',
        'onValue'   => 'required',
        'offLabel'  => 'Нет',
        'offValue'  => 'recommended',
        'onId'      => $this->id.'_filling_on_button',
        'offId'     => $this->id.'_filling_off_button',
    ),
    array(
        'hint' => 'При выборе "да" нельзя будет подать заявку без заполнения этого поля',
    )
);*/