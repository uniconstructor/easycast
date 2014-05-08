<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form TbActiveForm */
/* @var $this QEditActorUniversity */
/* @var $model QTheatreInstance */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// id анкеты
echo CHtml::hiddenField('qid', $this->questionary->id);
// театр
echo $form->select2Row($model, 'name',  array(
    'asDropDownList' => false,
    'options' => array(
        // допускаем вводсвоих вариантов
        'tags' => true,
        // не разрешаем выбирать больше 1 ВУЗа
        'maximumSelectionSize' => 1,
        // начинаем поиск с 2 символов
        'minimumInputLength'   => 2,
        // заглушка в пустом поле
        'placeholder'          => '(Не выбран)',
        // что отправляется если ничего не выбрано
        'placeholderOption'    => '',
        // разделители ввода (не допускаем пробел, чтобы можно было ввести )
        'tokenSeparators'      => array(',', "\n"),
        // добавляем возможность вводить несколько вариантов (нужно для добавления своего врянта)
        'multiplie'            => true,
        'ajax' => array(
            // url контроллера для получения списка ВУЗов
            'url'         => $this->optionsListUrl,
            // варинты списка приходят в формате JSON
            'dataType'    => 'json',
            // задержка в миллисекундах перед отправкой запроса
            'quietMillis' => 150,
            // параметры для поиска вариантов для выпадающего списка: 
            // первые введенные буквы, id анкеты и тип ВУЗа (музыкальный/театральный) 
            'data' => "js:function (term, page) {
                return {
                    'term': term,
                    'qid':  {$this->questionary->id},
                };
            }",
            // функция разбивки результатов поиска по страницам выпадающего списка
            'results' => "js:function(data, page) {
                return {
                    'results': data,
                };
            }",
        ),
        // эта функция создает "тег" из введенного пользователем значения, если
        // в списке стандартных значений не найдено ничего подходящего
        // Нужна, когда требуется добавить ВУЗ, которого нет у нас в списке
        'createSearchChoice' => "js:function(term, data) {
            if ($(data).filter(function() {
                return this.text.localeCompare(term) === 0;
            }).length === 0) {
            return {
                    id:   term,
                    text: term
                };
            }
        }",
    ),
));
// год начала
echo $form->datepickerRow($model, 'startyear', array(
    'options' => $this->getYearPickerOptions(),
));
// год окончания
echo $form->datepickerRow($model, 'stopyear', array(
    'options' => $this->getYearPickerOptions(),
));
// мастерская
echo $form->textFieldRow($model, 'director');
