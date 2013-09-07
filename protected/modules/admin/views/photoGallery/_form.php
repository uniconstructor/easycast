<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'photo-gallery-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Поля помеченные звездочкой <span class="required">*</span> обязательные.</p>

	<?php echo $form->errorSummary($model); ?>

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
	    echo '<div>Изображения</div>';
    ?>
    <?php
            if ($model->galleryBehavior->getGallery() === null)
            {
                echo '<p>Нужно сохранить галерею перед загрузкой изображений</p>';
            }else
           {
                $this->widget('GalleryManager', array(
                                                 'gallery' => $model->galleryBehavior->getGallery(),
                                                 'controllerRoute' => '/admin/gallery'
                                            ));
            }
    ?>

	<?php echo $form->checkBoxRow($model,'visible',array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
