<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// город проживания
echo $form->textFieldRow($model, 'city', 
    array(
        'size'        => 60,
        'maxlength'   => 128,
        'placeholder' => 'Город проживания',
    ),
    array(
        'prepend'     => '<i class="icon icon-map-marker"></i>',
    )
);




/*
Yii::import('ext.CountryCitySelectorRu.*');
$countryConfig['country']['default'] = 'RU';
$countrySelector = new CountryCitySelectorRu($countryConfig);
$countrySelector->controller = $this;

echo $form->labelEx($model, 'cityid');
$cityOptions = array(
    'sourceUrl' => Yii::app()->createUrl('questionary/questionary/ajax?type=city&parenttype=country&parentid=RU'),
);
$countrySelector->cityActiveField('cityid', $model, $cityOptions);
echo $form->error($model, 'cityid');
*/




/*
$tags = true;
if ( $model->questionary->cityid )
{
    $tags = array(
        array(
            'id'   => $model->questionary->cityid,
            'text' => $model->questionary->cityobj->name,
        ),
    );
}
    
echo $form->select2Row($model->questionary, 'cityid',  array(
    // не показываем выпадающий список
    'asDropDownList' => false,
    // параметры для JS-элемента select2
    'options' => array(
        // допускаем ввод своих вариантов
        'tags'                 => $tags,
        // не разрешаем выбирать больше 1 ВУЗа
        'maximumSelectionSize' => 1,
        // начинаем поиск с 2 символов
        'minimumInputLength'   => 0,
        // заглушка в пустом поле
        'placeholder'          => '(Не выбран)',
        // что отправляется если ничего не выбрано
        'placeholderOption'    => $model->questionary->cityid,
        // разделители ввода (не допускаем пробел, чтобы можно было ввести несколько слов в названии)
        'tokenSeparators'      => array(',', "\n"),
        // добавляем возможность вводить несколько вариантов (нужно для добавления своего варианта)
        'multiplie'            => true,
        // настройки AJAX для autocomplete
        'ajax' => array(
            // url контроллера для получения списка ВУЗов
            'url'         => Yii::app()->createUrl('/site/geoLookup'),
            // варинты списка приходят в формате JSON
            'dataType'    => 'json',
            // задержка в миллисекундах перед отправкой запроса
            'quietMillis' => 150,
            // параметры для поиска вариантов для выпадающего списка:
            // первые введенные буквы, id анкеты и тип ВУЗа (музыкальный/театральный)
            'data' => "js:function (term, page) {
                return {
                    'term'      : term,
                    'type'      : 'city',
                    'parenttype': 'country',
                    'parentid'  : 'RU',
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
    ),
    array(
        'prepend' => '<i class="icon icon-map-marker"></i>',
    )
);*/