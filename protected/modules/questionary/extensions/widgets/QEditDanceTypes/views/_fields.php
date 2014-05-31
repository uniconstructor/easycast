<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditDanceTypes */
/* @var $model QDanceType */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));

// стиль танца
echo $form->select2Row($model, 'name',  array(
    'asDropDownList' => false,
    'options' => array(
            // список вариантов указываем в tags а не в data уровнем выше, иначе не получится
            // добавить свой вариант (которого нет в списке)
            'tags' => $this->getActivityOptions('dancetype'),
            'maximumSelectionSize' => 1,
            'placeholder'          => '(Не выбран)',
            'placeholderOption'    => '',
            'tokenSeparators'      => array(',', ' ')
        ),
    )
);

// уровень подготовки
echo $form->select2Row($model, 'level',  array(
        'asDropDownList' => true,
        'data' => $this->questionary->getFieldVariants('level', false),
    )
);

