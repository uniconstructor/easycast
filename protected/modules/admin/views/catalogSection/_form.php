<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'catalog-section-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php // echo $form->textFieldRow($model,'parentid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php // echo $form->textFieldRow($model,'scopeid',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>128)); ?>

	<?php // echo $form->textFieldRow($model,'shortname',array('class'=>'span5','maxlength'=>128)); ?>

	<?php // echo $form->textFieldRow($model,'lang',array('class'=>'span5','maxlength'=>5)); ?>

	<?php
    	if ($model->galleryBehavior->getGallery() === null)
    	{
    	    echo '<p>Нужно сохранить категорию перед загрузкой логотипа</p>';
    	}else
    	{
    	    $this->widget('GalleryManagerS3', array(
    	        'gallery' => $model->galleryBehavior->getGallery(),
    	        'controllerRoute' => '/admin/gallery'
    	    ));
    	}
	?>

	<?php // echo $form->textFieldRow($model,'content',array('class'=>'span5','maxlength'=>8)); ?>

	<?php echo $form->textFieldRow($model,'order',array('class'=>'span5','maxlength'=>6)); ?>

	<?php // echo $form->textFieldRow($model,'count',array('class'=>'span5','maxlength'=>11)); ?>

	<?php echo $form->checkBoxRow($model,'visible',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
