<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditAwards */
/* @var $model QAward */

$countries = ECPurifier::getSelect2Options($this->createCountryList());

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// id анкеты
echo CHtml::hiddenField('qid', $this->questionary->id);

// название
echo $form->textFieldRow($model, 'name');
// номинация
echo $form->textFieldRow($model, 'nomination');
// страна
echo $form->select2Row($model, 'countryid',  array(
        'asDropDownList' => true,
        'data'           => $this->createCountryList(),
    )
);
// год
echo $form->datepickerRow($model, 'year', array(
    'options' => $this->getYearPickerOptions(),
));

