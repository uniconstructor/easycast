<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  ECEditVideo */
/* @var $model Video */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// название
echo $form->textFieldRow($model, 'name');
// ссылка
echo $form->textFieldRow($model, 'link');
// видимость
echo $form->dropDownListRow($model, 'visible', $model->getVisibleOptions(), null, array(
    'hint' => 'Да: видео будет видно в вашей анкете всем посетителям сайта<br>
        Нет: видео будет отображаться только администраторам при просмотре вашей заявки',
));