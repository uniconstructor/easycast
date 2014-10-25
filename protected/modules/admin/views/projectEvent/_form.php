<?php

/**
 * Форма редактирования мероприятия в админке
 * 
 * @todo указывать адрес вместо текстового поля "место проведения"
 */
/* @var $this  ProjectEventController */
/* @var $model ProjectEvent */
/* @var $form  TbActiveForm */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'project-event-form',
    'enableAjaxValidation' => false,
));
// данные формы нужны для корректной работы элемента выбора даты
$formData = Yii::app()->request->getParam('ProjectEvent');

// ошибки формы
echo Yii::t('coreMessages', 'form_required_fields',
    array('{mark}' => '<span class="required">*</span>'));
echo $form->errorSummary($model);

// название события
echo $form->textFieldRow($model, 'name',
    array('class' => 'span5', 'maxlength' => 255));

// тип мероприятия
$eventTypes = $model->getTypes();
echo $form->dropDownListRow($model, 'type', $eventTypes);

// группа мероприятия
$groups = $model->getOpenGroups($project->id);
if ( $model->isNewRecord AND $groupid )
{// создаем событие в заранее определенной группе
    $model->parentid = $groupid;
}
if ( $model->status != 'draft' )
{// нельзя перемещать активные мероприятия между группами
    echo $form->dropDownListRow($model, 'parentid', $groups,
        array('disabled' => 'disabled'));
}else
{// черновики можно
    echo $form->dropDownListRow($model, 'parentid', $groups);
}

// описание мероприятия
echo $form->redactorRow($model, 'description', array(),
    array(
    'hint' => 'Видно всем',
));
// Дополнительная информация для участников
echo $form->redactorRow($model, 'memberinfo', array(),
    array(
    'hint' => 'Отображается только подтвержденным участникам',
));
// нужно создать мероприятие без даты (она пока неизвестна)
// @todo выключать даты начала и окончания при установке этой галочки
echo $form->checkBoxRow($model, 'nodates');

// время начала
if ( isset($formData['timestart']) )
{
    $model->timestart = $formData['timestart'];
}elseif ( $model->timestart )
{
    $model->timestart = date(Yii::app()->params['outputDateTimeFormat'], (int)$model->timestart);
}else
{
    $model->timestart = null;
}
echo $form->dateTimePickerRow($model, 'timestart', array(
        'options' => array(
            'language'       => 'ru',
            'format'         => Yii::app()->params['inputDateTimeFormat'],
            'startView'      => 'month',
            'maxView'        => 'year',
            'startDate'      => date(Yii::app()->params['outputDateTimeFormat']),
            'weekStart'      => 1,
            'autoclose'      => true,
            'todayHighlight' => true,
            'minuteStep'     => 5,
        ),
    ),
    array(
        'hint'    => 'Если дата начала точно не известна - поставьте галочку "дата начала уточняется"',
        'prepend' => '<i class="icon-calendar"></i>',
    )
);

// время окончания
if ( isset($formData['timeend']) )
{
    $model->timeend = $formData['timeend'];
}elseif ( $model->timestart )
{
    $model->timeend = date(Yii::app()->params['outputDateTimeFormat'], (int)$model->timeend);
}else
{
    $model->timeend = null;
}
echo $form->dateTimePickerRow($model, 'timeend', array(
        'options' => array(
            'language'       => 'ru',
            'format'         => Yii::app()->params['inputDateTimeFormat'],
            'startView'      => 'month',
            'maxView'        => 'year',
            'startDate'      => date(Yii::app()->params['outputDateTimeFormat']),
            'weekStart'      => 1,
            'autoclose'      => true,
            'todayHighlight' => true,
            'minuteStep'     => 5,
        ),
    ),
    array(
        'hint'    => 'Если дата начала точно не известна - поставьте галочку "дата начала уточняется"',
        'prepend' => '<i class="icon-calendar"></i>',
    )
);

// время сбора
// @todo заменить этот виджет на более удобный: http://amsul.ca/pickadate.js/time.htm#formats
echo $form->labelEx($model, 'eta');
$this->widget('ext.ETinyTimePicker.ETinyTimePicker',
    array(
    'model'     => $model,
    'attribute' => 'eta',
));
echo $form->error($model, 'eta');

// Описание места встречи
echo $form->redactorRow($model, 'meetingplace', array(), array(
    'hint' => 'Отображается только подтвержденным участникам',
));
// показывать ли время начала съемок?
$showTimeStartOptions = array();
if ( $model->isNewRecord )
{
    $showTimeStartOptions['checked'] = false;
}
echo $form->checkBoxRow($model, 'showtimestart');
echo '<div class="alert">Если галочка поставлена - участникам покажется и время встречи, и время съемок.
    Если снята - только время сбора.</div>';

// Фотографии
echo '<div>Фотогалерея</div>';
if ( $model->photoGalleryBehavior->getGallery() === null )
{
    echo '<div class="alert">Нужно сохранить мероприятие перед загрузкой фотографий</div>';
}else
{
    $this->widget('GalleryManager', array(
        'gallery'         => $model->photoGalleryBehavior->getGallery(),
        'controllerRoute' => '/admin/gallery'
    ));
}
// кнопка сохранения
$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type'       => 'primary',
    'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить',
));
$this->endWidget();

if ( ! $model->isNewRecord )
{// виджет редактирования оповещений
    /*$this->widget('admin.extensions.SimpleEmailRedactor.SimpleEmailRedactor', array(
        'model'     => $model,
        'createUrl' => Yii::app()->createUrl('admin/admin/createListItem'),
        'updateUrl' => Yii::app()->createUrl('admin/admin/updateListItem'),
        'deleteUrl' => Yii::app()->createUrl('admin/admin/deleteListItem'),
    ));*/

    // все настройки
    $this->widget('ext.EditableConfig.EditableConfig', array(
        'objectType' => get_class($model),
        'objectId'   => $model->id,
        'createUrl'  => Yii::app()->createUrl('admin/admin/createListItem'),
        'updateUrl'  => Yii::app()->createUrl('admin/admin/updateListItem'),
        'deleteUrl'  => Yii::app()->createUrl('admin/admin/deleteListItem'),
    ));
}