<?php
/**
 * Список полей формы во всплывающем modal-окне
 * Структура своя для каждого виджета
 */
/* @var $form  TbActiveForm */
/* @var $this  EditExtraFields */
/* @var $model ExtraField */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));

// категория в которую попадает поле сразу после создания
//echo CHtml::hiddenField('categoryid', $this->categoryId);
?>
<div class="control-group">
    <?php
    echo CHtml::label('Категория в которую попадает поле после создания', $this->mainIdPrefix.'_categoryId', array(
        'class' => 'control-label',
    ));
    ?>
    <div class="controls">
        <?php
        echo CHtml::dropDownList('categoryId', $this->categoryId, $this->getCategoryOptions(), array(
            'id' => $this->mainIdPrefix.'_categoryId',
        ));
        ?>
    </div>
</div>
<?php 
// название для пользователя
echo $form->textFieldRow($model, 'label', array(), array(
    'hint' => 'Это название видит пользователь при заполнении',
));
// название (служебное)
echo $form->textFieldRow($model, 'name', array(), array(
    'hint' => 'Обязательное служебное поле. Только маленькие латинские буквы и знак подчеркивания. Без пробелов.
        Нельзя создавать два разных поля с одинаковым служебным именем.<br>
        Если вводите много полей для одной категории - то рекомендую добавлять в начале названия первые буквы проекта.<br>
        Пример:<br>
        Поле <b>"Расскажите о себе"</b> лучше назвать <b>"tm_about"</b><br>
        ("tm_" вначале потому что вопрос из проекта "топ-модель")',
));
// тип поля
echo $form->dropDownListRow($model, 'type', $model->getTypeOptions());
// описание
echo $form->textAreaRow($model, 'description', array('style' => 'width:100%;'), array(
    'hint' => 'Здесь можно подробно описать что и как заполнять (как этот текст). ',
        // @todo Можно ставить ссылки и использовать форматирование. Не меняйте цвета фона или текста.
));