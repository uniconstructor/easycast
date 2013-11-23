<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form TbActiveForm */
/* @var $this QEditFilms */
/* @var $model QEditActorUniversity */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// id анкеты
echo CHtml::hiddenField('qid', $this->questionary->id);
// ВУЗ
echo $form->select2Row($model, 'name',  array(
    'asDropDownList' => false,
    //'data' => $this->questionary->getFieldVariants('language'),
    'options' => array(
        //'tags' => ECPurifier::getSelect2Options($this->questionary->getFieldVariants('language', false)),
        'tags' => array(),
        // не разрешаем выбирать больше 1 ВУЗа
        'maximumSelectionSize' => 1,
        'minimumInputLength'   => 2,
        'placeholder'          => '(Не выбран)',
        'placeholderOption'    => '',
        'tokenSeparators'      => array(',', ' '),
        'ajax' => array(
            'url'         => $this->optionsListUrl,
            'dataType'    => 'json',
            'quietMillis' => 100,
            'data' => "js:function (term, page){
                return {
                'term': term,
                'qid':  {$this->questionary->id},
                'type': '".QUniversity::TYPE_MUSIC."'
                };
            }",
        'results' => "js:function(data, page){
            return {
                'results': data,
                'more':false
                };
            }",
        ),
    ),
));
// специальность
echo $form->textFieldRow($model, 'specialty');
// год окончания
echo $form->datepickerRow($model, 'year', array(
    'options' => $this->getYearPickerOptions(),
));
// мастерская
echo $form->textFieldRow($model, 'workshop');