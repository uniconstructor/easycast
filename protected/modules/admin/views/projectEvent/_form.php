<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'project-event-form',
	'enableAjaxValidation'=>false,
)); 

$dateFormatter = new CDateFormatter('ru');
?>

	<?php echo Yii::t('coreMessages','form_required_fields', array('{mark}' => '<span class="required">*</span>')); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php // echo $form->textFieldRow($model,'projectid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>255)); ?>

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

	<?php 
	    echo $form->labelEx($model, 'timestart');
	    $defaultStart = time();
	    if ( $model->timestart )
	    {
	        $defaultStart = $model->timestart;
	    }
	    $this->widget('ext.CJuiDateTimePicker.CJuiDateTimePicker',array(
	        //'model'=>$model,
            //'flat' => true,
	        //'attribute'=>'timestart',
            'name' => 'ProjectEvent[timestart]',
            'value' => $dateFormatter->format('dd.MM.yyyy HH:mm', $defaultStart),
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
	    $this->widget('ext.CJuiDateTimePicker.CJuiDateTimePicker',array(
	        //'model'=>$model,
            //'flat' => true,
	        //'attribute'=>'timeend',
            'name' => 'ProjectEvent[timeend]',
            'value' => $dateFormatter->format('dd.MM.yyyy HH:mm', $defaultEnd),
	        'options'=>array(
	            'showAnim'=>'fold',
	        ),
	    ));
	?>
	
	<?php
	    echo '<div>Фотогалерея</div>';
    ?>
    <?php
            if ($model->photoGalleryBehavior->getGallery() === null)
            {
                echo '<p>Нужно сохранить мероприятие перед загрузкой фотографий</p>';
            }else
           {
                $this->widget('GalleryManagerS3', array(
                                                 'gallery' => $model->photoGalleryBehavior->getGallery(),
                                                 'controllerRoute' => '/questionary/gallery'
                                            ));
            }
    ?>

	<?php // echo $form->textFieldRow($model,'addressid',array('class'=>'span5','maxlength'=>11)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
