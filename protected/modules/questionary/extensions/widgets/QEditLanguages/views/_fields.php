<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form TbActiveForm */
/* @var $this QEditFilms */
/* @var $model QFilmInstance */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// язык
echo $form->select2Row($model, 'language',  array(
    'asDropDownList' => false,
    //'data' => $this->questionary->getFieldVariants('language'),
    'options' => array(
        'tags' => ECPurifier::getSelect2Options($this->questionary->getFieldVariants('language', false)),
        'maximumSelectionSize' => 1,
        'placeholder' => '(Не выбран)',
        'placeholderOption' => '',
        'tokenSeparators' => array(',', ' ')
        ),
    )
);
// уровень владения
//echo $form->dropDownListRow($model, 'level', $this->questionary->getFieldVariants('languagelevel'));
echo $form->select2Row($model, 'level',  array(
    'asDropDownList' => true,
    'data' => $this->questionary->getFieldVariants('languagelevel', false),
    )
);