<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'project-form',
	'enableAjaxValidation'=>false,
)); 

$dateFormatter = new CDateFormatter('ru');
?>

	<?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->dropDownListRow($model,'type', $model->getTypeList()); ?>

	<?php echo $form->labelEx($model,'shortdescription'); ?>
    <?php 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'shortdescription',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
    ?>
    <?php echo $form->error($model,'shortdescription'); ?>
    
	<?php echo $form->labelEx($model,'description'); ?>
    <?php 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'description',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
    ?>
    <?php echo $form->error($model,'description'); ?>
    
	<?php echo $form->labelEx($model,'customerdescription'); ?>
    <?php 
        $this->widget('ext.imperavi-redactor-widget.ImperaviRedactorWidget', array(
    	'model' => $model,
    	'attribute' => 'customerdescription',
    	'options' => array(
    		'lang'    => 'ru',
            ),
        ));
    ?>
    <?php echo $form->error($model,'customerdescription'); ?>

	<?php
	    echo '<div>Логотип</div>';
    ?>
    <?php
            if ($model->galleryBehavior->getGallery() === null)
            {
                echo '<p>Нужно сохранить проект перед загрузкой логотипа</p>';
            }else
           {
                $this->widget('GalleryManagerS3', array(
                                                 'gallery' => $model->galleryBehavior->getGallery(),
                                                 'controllerRoute' => '/questionary/gallery'
                                            ));
            }
    ?>
    
	<?php 
	    echo $form->labelEx($model, 'timestart');
	    $defaultStart = time();
	    if ( $model->timestart )
	    {
	        $defaultStart = $model->timestart;
	    }
	    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
	        //'model'=>$model,
            //'flat' => true,
	        //'attribute'=>'timestart',
            'name' => 'Project[timestart]',
            'value' => $dateFormatter->format('dd/MM/yyyy', $defaultStart),
	        'options'=>array(
	            'showAnim'=>'fold',
	        ),
	    ));
	?>

	<?php 
	    echo $form->labelEx($model, 'timeend');
	    $defaultEnd = time();
	    if ( $model->timeend )
	    {
	        $defaultEnd = $model->timeend;
	    }
	    $this->widget('zii.widgets.jui.CJuiDatePicker',array(
	        //'model'=>$model,
            //'flat' => true,
	        //'attribute'=>'timeend',
            'name' => 'Project[timeend]',
            'value' => $dateFormatter->format('dd/MM/yyyy', $defaultEnd),
	        'options'=>array(
	            'showAnim'=>'fold',
	        ),
	    ));
	?>

	<?php echo $form->dropDownListRow($model,'leaderid', $model->getManagerList()); ?>
	
	<?php echo $form->dropDownListRow($model,'supportid', $model->getManagerList(true)); ?>

	<?php // echo $form->textFieldRow($model,'customerid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php // echo $form->textFieldRow($model,'orderid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->checkBoxRow($model,'isfree',array('class'=>'span5')); ?>
	
	<?php
	    echo '<div>Фотогалерея</div>';
    ?>
    <?php
            if ($model->photoGalleryBehavior->getGallery() === null)
            {
                echo '<p>Нужно сохранить проект перед загрузкой фотографий</p>';
            }else
           {
                $this->widget('GalleryManagerS3', array(
                                                 'gallery' => $model->photoGalleryBehavior->getGallery(),
                                                 'controllerRoute' => '/questionary/gallery'
                                            ));
            }
    ?>
    
    <fieldset>
            <legend>Видео</legend>
            <?php
                echo $form->errorSummary($validatedVideos);
            ?>
            
            <?php
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
            ?>
    </fieldset>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
		)); ?>
	</div>
	
<?php $this->endWidget(); ?>
