<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditLanguages */
/* @var $model QLanguage */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// язык
echo $form->select2Row($model, 'language',  array(
    'asDropDownList' => false,
    'options' => array(
            // список вариантов указываем в tags а не в data уровнем выше, иначе не получится
            // добавить свой вариант (которого нет в списке)
            'tags' => ECPurifier::getSelect2Options($this->questionary->getFieldVariants('language', false)),
            'maximumSelectionSize' => 1,
            'placeholder'          => '(Не выбран)',
            'placeholderOption'    => '',
            'tokenSeparators'      => array(',', ' ')
        ),
    )
);
// уровень владения
echo $form->select2Row($model, 'level',  array(
        'asDropDownList' => true,
        'data' => $this->questionary->getFieldVariants('languagelevel', false),
    )
);