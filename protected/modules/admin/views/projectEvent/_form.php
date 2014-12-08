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
// форматирование даты
$dateFormat = Yii::app()->params['yiiDateFormat'];

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
// @todo группы событий временно сотключены: от них пока проблем быльше чем пользы
/*$groups = $model->getOpenGroups($project->id);
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
}*/

// описание мероприятия
echo $form->redactorRow($model, 'description', array(),
    array(
    'hint' => 'Видно всем',
));
// Дополнительная информация для участников
echo $form->redactorRow($model, 'memberinfo', array(), array(
    'hint' => 'Отображается только подтвержденным участникам',
));
// нужно создать мероприятие без даты (она пока неизвестна)
// @todo выключать даты начала и окончания при установке этой галочки
echo $form->checkBoxRow($model, 'nodates');

// время начала
echo $form->datepickerRow($model, 'timestart', array(
        'options' => array(
            'language'       => 'ru',
            'format'         => Yii::app()->params['inputDateTimeFormat'],
            'startView'      => 'month',
            'maxView'        => 'year',
            'startDate'      => '+0d',
            'weekStart'      => 1,
            'autoclose'      => true,
            'todayHighlight' => true,
            'minuteStep'     => 10,
        ),
        'htmlOptions' => array(
            'value' => Yii::app()->dateFormatter->format($dateFormat, $model->timestart),
        ),
    ),
    array(
        'hint'    => 'Если дата начала точно не известна - поставьте галочку "дата начала уточняется"',
        'prepend' => '<i class="icon-calendar"></i>',
    )
);
// время окончания
echo $form->datepickerRow($model, 'timeend', array(
        'options' => array(
            'language'       => 'ru',
            'format'         => Yii::app()->params['inputDateTimeFormat'],
            'startView'      => 'month',
            'maxView'        => 'year',
            'startDate'      => '+0d',
            'weekStart'      => 1,
            'autoclose'      => true,
            'todayHighlight' => true,
            'minuteStep'     => 10,
        ),
        'htmlOptions' => array(
            'value' => Yii::app()->dateFormatter->format($dateFormat, $model->timeend),
        ),
    ),
    array(
        'hint'    => 'Если дата окончания точно не известна - поставьте галочку "дата окончания уточняется"',
        'prepend' => '<i class="icon-calendar"></i>',
    )
);

// время сбора
// @todo заменить этот виджет на более удобный: http://amsul.ca/pickadate.js/time.htm#formats
echo $form->labelEx($model, 'eta');
$this->widget('ext.ETinyTimePicker.ETinyTimePicker', array(
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
{// виджет редактирования настроек
    /*$this->widget('admin.extensions.SimpleEmailRedactor.SimpleEmailRedactor', array(
        'model'     => $model,
        'createUrl' => Yii::app()->createUrl('admin/admin/createListItem'),
        'updateUrl' => Yii::app()->createUrl('admin/admin/updateListItem'),
        'deleteUrl' => Yii::app()->createUrl('admin/admin/deleteListItem'),
    ));*/

    // все настройки
    /*$this->widget('ext.EditableConfig.EditableConfig', array(
        'objectType' => get_class($model),
        'objectId'   => $model->id,
        'createUrl'  => Yii::app()->createUrl('admin/admin/createListItem'),
        'updateUrl'  => Yii::app()->createUrl('admin/admin/updateListItem'),
        'deleteUrl'  => Yii::app()->createUrl('admin/admin/deleteListItem'),
    ));*/
}