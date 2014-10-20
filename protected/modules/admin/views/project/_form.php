<?php 
/**
 * Форма редактирования проекта
 */
/* @var $model Project */
/* @var $form  TbActiveForm */
/* @var $bannerForm CActiveForm */
/* @var $this ProjectController */

// баннер
// @todo заменить стандартным виджетом настроек
echo '<div><b>Баннер</b></div>';
if ( $model->isNewRecord )
{
    echo '<p>Нужно сохранить проект перед загрузкой баннера</p>';
}else
{
    $currentFile = null;
    $bannerConfig = $model->getConfigObject('banner');
    /* @var $bannerFile ExternalFile */
    if ( ! $bannerFile = $bannerConfig->getValueObject() )
    {
        echo '<div class="alert alert-info">Баннер не загружен</div>';
    }else
    {
        $currentFile = $bannerFile->name;
        $img = CHtml::link(CHtml::image($bannerFile->url, $bannerFile->oldname), $bannerFile->url);
        echo '<div class="well">'.$img.'</div>';
    }
    
    $bannerForm = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'      => 'project-banner-form',
        'method'  => 'post',
        'action'  => array('uploadBanner', 'projectId' => $model->id),
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data' ),
    ));
     
    echo CHtml::hiddenField('projectId', $model->id);
    echo CHtml::hiddenField('pk', $bannerConfig->id);
    // форма загрузки файла
    echo CHtml::fileField('file', $currentFile);
    //echo $bannerForm->fileFieldRow($bannerFile, 'file');
    // кнопка загрузки файла
    $this->widget('bootstrap.widgets.TbButton', array(
        'buttonType' => 'submit',
        'type'       => 'primary',
        'label'      => 'Загрузить баннер',
    ));
    $this->endWidget();
}

// форма редактирования проекта
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'project-form',
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
// кнопка сохранения 
$this->widget('bootstrap.widgets.TbButton', array(
	'buttonType' => 'submit',
	'type'       => 'primary',
	'label'      => $model->isNewRecord ? Yii::t('coreMessages', 'create') : Yii::t('coreMessages', 'save'),
)); 
// конец формы
$this->endWidget();

?>
<fieldset>
    <legend>Видео</legend>
    <?php
    // список видео
    if ( ! $model->isNewRecord )
    {// не показываем добавление видео при создании проекта - его не к чему прикреплять пока проект не создан
        // список видео
        $this->widget('ext.ECEditVideo.ECEditVideo', array(
            'objectType'  => 'project',
            'objectId'    => $model->id,
            'clipModule'  => 'admin',
        ));
    }else
    {
        echo '<div class="alert">Нужно сохранить проект перед добавлением видео</div>';
    }
    ?>
</fieldset>
<?php 
$clips = Yii::app()->getModule('admin')->formClips;
foreach ( $clips as $clip )
{
    echo $this->clips[$clip];
}