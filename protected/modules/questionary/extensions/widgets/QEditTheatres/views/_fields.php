<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  QEditTheatres */
/* @var $model QTheatreInstance */

$stopYearDisplay = 'block';
if ( $model->currently )
{// не показываем год окончания для текущего места работы
    $stopYearDisplay = 'none';
}

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// id анкеты
echo CHtml::hiddenField('qid', $this->questionary->id);

// театр
echo $form->select2Row($model, 'name',  array(
    'asDropDownList' => false,
    'options' => array(
        // допускаем ввод своих вариантов
        'tags' => true,
        // не разрешаем выбирать или вводить больше одного значения
        'maximumSelectionSize' => 1,
        // начинаем поиск с 2 символов
        'minimumInputLength'   => 2,
        // заглушка в пустом поле
        'placeholder'          => '(Не выбран)',
        // что отправляется если ничего не выбрано
        'placeholderOption'    => '',
        // разделители ввода (не допускаем пробел, чтобы можно было ввести название из нескольких слов)
        'tokenSeparators'      => array(',', "\n"),
        // добавляем возможность вводить несколько вариантов (нужно для добавления своего варианта)
        'multiplie'            => true,
        // настройки AJAX для autocomplete
        'ajax' => array(
            // url контроллера для получения списка театров
            'url'         => $this->optionsListUrl,
            // варианты списка приходят в формате JSON
            'dataType'    => 'json',
            // задержка в миллисекундах перед отправкой запроса
            'quietMillis' => 150,
            // параметры для поиска вариантов для выпадающего списка: 
            // первые введенные буквы, id анкеты 
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
        // Нужна, когда требуется добавить театр, которого нет у нас в списке
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
// это текущее место работы?
echo $form->widgetRow('ext.ECMarkup.ECToggleInput.ECToggleInput', array(
    'model'     => $model,
    'attribute' => 'currently',
    'onLabel'   => 'Да',
    'onValue'   => '1',
    'offLabel'  => 'Нет',
    'offValue'  => '0',
    'afterOn'   => '$("#'.$this->id.'-stopyear-wrapper").hide();',
    'afterOff'  => '$("#'.$this->id.'-stopyear-wrapper").show();',
    'onId'      => $this->id.'_currently_on_button',
    'offId'     => $this->id.'_currently_off_button',
));
// год начала работы в театре
echo $form->datepickerRow($model, 'startyear', array(
    'options' => $this->getYearPickerOptions(),
));
// год окончания работы в театре
echo '<div id="'.$this->id.'-stopyear-wrapper" style="display:'.$stopYearDisplay.';">';
echo $form->datepickerRow($model, 'stopyear', array(
    'options' => $this->getYearPickerOptions(),
), array(
    'hint' => 'Не заполняется для текущего места работы',
));
echo '</div>';
// режиссер
echo $form->textFieldRow($model, 'director');
