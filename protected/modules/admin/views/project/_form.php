<?php 
/**
 * Форма редактирования проекта
 */
/* @var $model      Project */
/* @var $form       TbActiveForm */
/* @var $bannerForm CActiveForm */
/* @var $this       ProjectController */

//$params = $model->getConfig();
//CVarDumper::dump($params, 10, true);


// баннер
// @todo заменить стандартным виджетом настроек
echo '<div><b>Баннер</b></div>';
if ( $model->isNewRecord )
{
    echo '<p>Нужно сохранить проект перед загрузкой баннера</p>';
}else
{
    $bannerConfig = $model->getConfigObject('banner');
    $this->widget('ext.ECMarkup.ECConfigImageField.ECConfigImageField', array(
        'config'      => $bannerConfig,
        'formOptions' => array(
            'id'     => 'project-banner-form',
            'action' => array('uploadBanner', 'projectId' => $model->id),
        ),
        'hiddenFields' => array(
            'projectId' => $model->id,
            'pk'        => $bannerConfig->id,
        ),
    ));
    // @todo текст под баннером (отображается вместо описания роли)
    $greetingConfig = $model->getConfigObject('customGreeting');
    /*
     *     $this->widget(
    'bootstrap.widgets.TbEditableField',
    array(
    'type' => 'text',
    'model' => $model,
    'attribute' => 'name', // $model->name will be editable
    'url' => $endpoint, //url for submit data
    )
    );
     */
}
// форма редактирования проекта
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'project-form',
	'enableAjaxValidation' => false,
));
// модуль User
$userModule = Yii::app()->getModule('user');
// данные формы нужны для корректной работы элемента выбора даты
$formData = Yii::app()->request->getParam('Project');
// рейтинг проекта (задается только здесь, в админке. Нужен только для сортировки проектов 
// при отображении для для заказчика и в коммерческом предложении)
$ratings = array('0' => 'Нет');
for ( $i = 0; $i <= 1000; $i++ )
{// ключи массива должны быть строковыми иначе плохо работает dropdownlist
    $ratings["$i"] = (string)$i;
}

echo Yii::t('coreMessages', 'form_required_fields', array('{mark}' => '<span class="required">*</span>'));
echo $form->errorSummary($model);
// рейтинг 
echo $form->dropDownListRow($model, 'rating', $ratings);
// название проекта
echo $form->textFieldRow($model, 'name');
// email проекта
echo $form->textFieldRow($model, 'email');
// тип проекта
echo $form->dropDownListRow($model, 'typeid', $model->getTypesList()); 
// краткое описание проекта
echo $form->widgetRow('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
	'model'     => $model,
	'attribute' => 'shortdescription',
	'options'   => array('lang' => 'ru'),
));
// описание для участника
echo $form->widgetRow('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    'model'     => $model,
    'attribute' => 'description',
    'options'   => array('lang' => 'ru'),
));
// описание для заказчика
echo $form->widgetRow('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    'model'     => $model,
    'attribute' => 'customerdescription',
    'options'   => array('lang' => 'ru'),
));

// логотип
echo '<div><b>Логотип</b></div>';
if ( $model->galleryBehavior->getGallery() === null )
{
    echo '<p>Нужно сохранить проект перед загрузкой логотипа</p>';
}else
{
    $this->widgetRow('GalleryManager', array(
         'gallery'         => $model->gallery,
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
        'hint'    => 'Если планируется длительный проект - поставьте галочку "Дата окончания неизвестна"',
        'prepend' => '<i class="icon-calendar"></i>',                
    )
);

// создать длительный без даты окончания
echo $form->checkBoxRow($model, 'notimeend');
// руководитель проекта
echo $form->dropDownListRow($model, 'leaderid',  $userModule::getAdminList());
// помощник 
echo $form->dropDownListRow($model, 'supportid', $userModule::getAdminList(true)); 
// @todo заказчик
// echo $form->dropDownListRow($model, 'customerid',  $customers);
// некоммерческий проект 
echo $form->checkBoxRow($model, 'isfree');
// фотки с проекта
echo '<div>Фотогалерея</div>';
if ( ! $model->gallery )
{
    echo '<div class="alert">Нужно сохранить проект перед загрузкой фотографий</div>';
}else
{
    $this->widgetRow('GalleryManager', array(
         'gallery'         => $model->gallery,
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
// выводим элементы формы добавления видео
foreach ( Yii::app()->getModule('admin')->formClips as $clip )
{
    echo $this->clips[$clip];
}