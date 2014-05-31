<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditAddChar */
/* @var $model QAddChar */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// язык
echo $form->select2Row($model, 'name',  array(
    'asDropDownList' => false,
    'options' => array(
            // список вариантов указываем в tags а не в data уровнем выше, иначе не получится
            // добавить свой вариант (которого нет в списке)
            'tags' => $this->getActivityOptions('addchar'),
            'maximumSelectionSize' => 1,
            'placeholder'          => '(выбрать)',
            'placeholderOption'    => '',
            'tokenSeparators'      => array(',')
        ),
    )
);
// доп. комментарий
echo $form->textAreaRow($model, 'comment', null, array(
    'hint' => 'Не обязательно',
));