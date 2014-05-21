<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form TbActiveForm */
/* @var $this QEditTvshows */
/* @var $model QTvshowInstance */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// id анкеты
echo CHtml::hiddenField('qid', $this->questionary->id);
// название телеканала
echo $form->textFieldRow($model, 'channelname');
// название телепроекта
echo $form->textFieldRow($model, 'projectname');
// год начала
echo $form->datepickerRow($model, 'startyear', array(
    'options' => $this->getYearPickerOptions(),
));
// год окончания
echo $form->datepickerRow($model, 'stopyear', array(
    'options' => $this->getYearPickerOptions(),
));
