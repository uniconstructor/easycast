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
// id анкеты
echo CHtml::hiddenField('qid', $this->questionary->id);
// название
echo $form->textFieldRow($model, 'name');
// ссылка
echo $form->textFieldRow($model, 'link');