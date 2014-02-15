<?php 
/**
 * Форма редактирования проекта
 */
/* @var $model Project */
/* @var $form TbActiveForm */


$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'                   => 'project-form',
	'enableAjaxValidation' => false,
));

$formData = Yii::app()->request->getParam('Project');

// рейтинг проекта (задается только здесь, в админке. Нужен только для сортировки проектов)
$ratings = array(
    '0' => 'Нет',
    '1'  => '1',
    '2'  => '2',
    '3'  => '3',
    '4'  => '4',
    '5'  => '5',
    '6'  => '6',
    '7'  => '7',
    '8'  => '8',
    '9'  => '9',
    '10' => '10',
);

$dateFormatter = new CDateFormatter('ru');
?>
	<?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>
	<?php echo $form->errorSummary($model); ?>
	
	<?php echo $form->dropDownListRow($model,'rating', $ratings); ?>
	
	<?php echo $form->textFieldRow($model, 'name',array('class' => 'span5', 'maxlength' => 255)); ?>
	
	<?php echo $form->dropDownListRow($model,'type', $model->getTypeList()); ?>
	
	<?php echo $form->labelEx($model,'shortdescription'); ?>
    <?php 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model'     => $model,
    	'attribute' => 'shortdescription',
    	'options'   => array(
    		'lang' => 'ru',
            ),
        ));
    ?>
    <?php echo $form->error($model,'shortdescription'); ?>
    
	<?php echo $form->labelEx($model,'description'); ?>
    <?php 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model'     => $model,
    	'attribute' => 'description',
    	'options'   => array(
    		'lang' => 'ru',
            ),
        ));
    ?>
    <?php echo $form->error($model,'description'); ?>
    
	<?php echo $form->labelEx($model,'customerdescription'); ?>
    <?php 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model'     => $model,
    	'attribute' => 'customerdescription',
    	'options' => array(
    		'lang' => 'ru',
            ),
        ));
    ?>
    <?php echo $form->error($model,'customerdescription'); ?>

    <?php
        echo '<div><b>Логотип</b></div>';
        if ( $model->galleryBehavior->getGallery() === null )
        {
            echo '<p>Нужно сохранить проект перед загрузкой логотипа</p>';
        }else
        {
            $this->widget('GalleryManager', array(
                 'gallery' => $model->galleryBehavior->getGallery(),
                 'controllerRoute' => '/admin/gallery'
            ));
        }
    ?>
    
    <?php 
	    // дата начала проекта
        $defaultStart = '';
    	if ( isset($formData['timestart']) )
    	{
    	    $defaultStart = $formData['timestart'];
    	}elseif ( $model->timestart )
    	{
    	    $defaultStart = date(Yii::app()->params['outputDateFormat'], (int)$model->timestart);
    	}
    	echo $form->datepickerRow($model, 'timestart', array(
    	        'options' => array(
    	            'language'  => 'ru',
    	            'format'    => 'dd.mm.yyyy',
    	            'startView' => 'month',
    	            'weekStart' => 1,
    	            'autoclose' => true,
    	        ),
                'value'   => $defaultStart,
    	    ),
            array(
                'hint'    => 'Если дата начала точно не известна - поставьте галочку "дата начала уточняется"',
                'prepend' => '<i class="icon-calendar"></i>',
            )
    	);
        // создать проект без даты начала
        echo $form->checkBoxRow($model, 'notimestart');
	?>

	<?php // дата окончания проекта
	    $defaultEnd = '';
    	if ( isset($formData['timeend']) )
    	{
    	    $defaultEnd = $formData['timeend'];
    	}elseif ( $model->timeend )
    	{
    	    $defaultEnd = date(Yii::app()->params['outputDateFormat'], (int)$model->timeend);
    	}
    	echo $form->datepickerRow($model, 'timeend', array(
    	        'options' => array(
    	            'language'  => 'ru',
    	            'format'    => 'dd.mm.yyyy',
    	            'startView' => 'month',
    	            'weekStart' => 1,
    	            'autoclose' => true,
    	        ),
                'value' => $defaultEnd,
    	    ),
            array(
                'hint'    => 'Если планируется длительный проект - поставьте галочку "без даты окончания"',
                'prepend' => '<i class="icon-calendar"></i>',                
            )
    	);
        // создать "бесконечный проект" - без даты окончания
        echo $form->checkBoxRow($model, 'notimeend');
	?>

	<?php echo $form->dropDownListRow($model,'leaderid',  $model->getManagerList()); ?>
	<?php echo $form->dropDownListRow($model,'supportid', $model->getManagerList(true)); ?>
	<?php // echo $form->textFieldRow($model,'customerid',array('class'=>'span5','maxlength'=>11)); ?>
	<?php // echo $form->textFieldRow($model,'orderid',array('class'=>'span5','maxlength'=>11)); ?>
	<?php echo $form->checkBoxRow($model, 'isfree', array('class' => 'span5')); ?>
	
    <?php
        echo '<div>Фотогалерея</div>';
        if ( $model->photoGalleryBehavior->getGallery() === null )
        {
            echo '<div class="alert">Нужно сохранить проект перед загрузкой фотографий</div>';
        }else
        {
            $this->widget('GalleryManager', array(
                 'gallery' => $model->photoGalleryBehavior->getGallery(),
                 'controllerRoute' => '/admin/gallery'
            ));
        }
    ?>
    
    <fieldset>
        <legend>Видео</legend>
        <?php
        if ( ! $model->isNewRecord )
        {// не показываем добавление видео при создании проекта - оно там не нужно и отвлекает
            echo $form->errorSummary($validatedVideos);
            // список видео
            $videoFormConfig = $video->formConfig();
            $this->widget('ext.multimodelform.MultiModelForm',array(
                'addItemText'   => Yii::t('coreMessages','add'),
                'removeText'    => Yii::t('coreMessages','delete'),
                'removeConfirm' => 'Удалить это видео?',
                'id'            => 'id_video', //the unique widget id
                'formConfig'    => $videoFormConfig, //the form configuration array
                'model'         => $video, //instance of the form model
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
    	$this->widget('bootstrap.widgets.TbButton', array(
    		'buttonType' => 'submit',
    		'type'       => 'primary',
    		'label'      => $model->isNewRecord ? 'Создать' : 'Сохранить',
    	)); 
	?>
	
<?php $this->endWidget(); ?>
