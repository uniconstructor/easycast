<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditFilms */
/* @var $model QActorUniversity */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// id анкеты
echo CHtml::hiddenField('qid', $this->questionary->id);

// ВУЗ
echo $form->select2Row($model, 'name',  array(
    // не показываем выпадающий список
    'asDropDownList' => false,
    // параметры для JS-элемента select2
    'options' => array(
        // допускаем вводсвоих вариантов
        'tags'                 => true,
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
                    'type': '".QUniversity::TYPE_THEATRE."'
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

// специальность
echo $form->textFieldRow($model, 'specialty');
// год окончания
echo $form->datepickerRow($model, 'year', array(
    'options' => $this->getYearPickerOptions(),
));
// мастерская
echo $form->textFieldRow($model, 'workshop');

