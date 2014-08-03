<?php 
/**
 * Форма редактирования проекта
 */
/* @var $model Project */
/* @var $form  TbActiveForm */


$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'                   => 'project-form',
	'enableAjaxValidation' => false,
));
// данные формы нужны для корректной работы элемента выбора даты
$formData = Yii::app()->request->getParam('Project');

// рейтинг проекта (задается только здесь, в админке. Нужен только для сортировки проектов 
// при отображении для для заказчика и в коммерческом предложении)
$ratings = array('0' => 'Нет');
for ( $i = 0; $i <= 1000; $i++ )
{
    $ratings["$i"] = (string)$i;
}

echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>'));
echo $form->errorSummary($model);
// рейтинг 
echo $form->dropDownListRow($model, 'rating', $ratings);
// название проекта
echo $form->textFieldRow($model, 'name', array('class' => 'span5', 'maxlength' => 255));
// email проекта
echo $form->textFieldRow($model, 'email', array('class' => 'span5', 'maxlength' => 255));
// тип проекта
echo $form->dropDownListRow($model, 'type', $model->getTypeList()); 

// краткое описание проекта
echo $form->labelEx($model, 'shortdescription');
$this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
	'model'     => $model,
	'attribute' => 'shortdescription',
	'options'   => array('lang' => 'ru'),
));
echo $form->error($model,'shortdescription');

// описание для участника
echo $form->labelEx($model, 'description');
$this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
	'model'     => $model,
	'attribute' => 'description',
	'options'   => array('lang' => 'ru'),
));
echo $form->error($model,'description');

// описание для заказчика
echo $form->labelEx($model, 'customerdescription');
$this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
	'model'     => $model,
	'attribute' => 'customerdescription',
	'options'   => array('lang' => 'ru'),
));
echo $form->error($model, 'customerdescription');

// логотип
echo '<div><b>Логотип</b></div>';
if ( $model->galleryBehavior->getGallery() === null )
{
    echo '<p>Нужно сохранить проект перед загрузкой логотипа</p>';
}else
{
    $this->widget('GalleryManager', array(
         'gallery'         => $model->galleryBehavior->getGallery(),
         'controllerRoute' => '/admin/gallery'
    ));
}

// дата начала проекта
$defaultStart = '';
if ( isset($formData['timestart']) )
{
    $model->timestart = $formData['timestart'];
}elseif ( $model->timestart )
{
    $model->timestart = date(Yii::app()->params['outputDateFormat'], (int)$model->timestart);
}else
{
    $model->timestart = null;
}
echo $form->datepickerRow($model, 'timestart', array(
        'options' => array(
            'language'       => 'ru',
            'format'         => Yii::app()->params['inputDateFormat'],
            'startView'      => 'month',
            'weekStart'      => 1,
            'autoclose'      => true,
            'todayHighlight' => true,
        ),
    ),
    array(
        'hint'    => 'Если дата начала точно не известна - поставьте галочку "дата начала уточняется"',
        'prepend' => '<i class="icon-calendar"></i>',
    )
);
// создать проект без даты начала
echo $form->checkBoxRow($model, 'notimestart');

// дата окончания проекта
$defaultEnd = '';
if ( isset($formData['timeend']) )
{
    $model->timeend = $formData['timeend'];
}elseif ( $model->timeend )
{
    $model->timeend = date(Yii::app()->params['outputDateFormat'], (int)$model->timeend);
}else
{
    $model->timeend = null;
}
echo $form->datepickerRow($model, 'timeend', array(
        'options' => array(
            'language'       => 'ru',
            'format'         => Yii::app()->params['inputDateFormat'],
            'startView'      => 'month',
            'weekStart'      => 1,
            'autoclose'      => true,
            'todayHighlight' => true,
        ),
    ),
    array(
        'hint'    => 'Если планируется длительный проект - поставьте галочку "без даты окончания"',
        'prepend' => '<i class="icon-calendar"></i>',                
    )
);
// создать "бесконечный проект" - без даты окончания
echo $form->checkBoxRow($model, 'notimeend');

// руководитель проекта
echo $form->dropDownListRow($model, 'leaderid',  $model->getManagerList());
// помошник 
echo $form->dropDownListRow($model, 'supportid', $model->getManagerList(true)); 
// заказчик
// echo $form->textFieldRow($model, 'customerid', array('class'=>'span5','maxlength'=>11));
// некоммерческий проект 
echo $form->checkBoxRow($model, 'isfree', array('class' => 'span5'));
// фотки с проекта
echo '<div>Фотогалерея</div>';
if ( $model->photoGalleryBehavior->getGallery() === null )
{
    echo '<div class="alert">Нужно сохранить проект перед загрузкой фотографий</div>';
}else
{
    $this->widget('GalleryManager', array(
         'gallery'         => $model->photoGalleryBehavior->getGallery(),
         'controllerRoute' => '/admin/gallery',
    ));
}
?>
<fieldset>
    <legend>Видео</legend>
    <?php
    // список видео
    // @todo убрать использование multiModelForm, заменить на нашу версию Grid view
    if ( ! $model->isNewRecord )
    {// не показываем добавление видео при создании проекта - оно там не нужно и отвлекает
        echo $form->errorSummary($validatedVideos);
        $videoFormConfig = $video->formConfig();
        $this->widget('ext.multimodelform.MultiModelForm', array(
            'addItemText'    => Yii::t('coreMessages','add'),
            'removeText'     => Yii::t('coreMessages','delete'),
            'removeConfirm'  => 'Удалить это видео?',
            'id'             => 'id_video', //the unique widget id
            'formConfig'     => $videoFormConfig, //the form configuration array
            'model'          => $video, //instance of the form model
            'validatedItems' => $validatedVideos,
            // ранее сохраненные видео
            'data' => $model->videos,
        ));
    }else
    {
        echo '<div class="alert">Нужно сохранить проект перед добавлением видео</div>';
    }
    ?>
</fieldset>
<?php
// кнопка сохранения 
$this->widget('bootstrap.widgets.TbButton', array(
	'buttonType' => 'submit',
	'type'       => 'primary',
	'label'      => $model->isNewRecord ? Yii::t('coreMessages', 'create') : Yii::t('coreMessages', 'save'),
)); 

$this->endWidget();
