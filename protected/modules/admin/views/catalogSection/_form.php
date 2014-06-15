<?php 
/**
 * Форма редактирования раздела каталога
 */

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id' => 'catalog-section-form',
	'enableAjaxValidation' => false,
));
?>
	<?php echo $form->errorSummary($model); ?>
	<?php echo $form->textFieldRow($model,'name'); ?>
	<?php echo $form->textFieldRow($model,'shortname'); ?>
	<?php
	/*if ( $model->galleryBehavior->getGallery() === null )
	{
	    echo '<p>Нужно сохранить категорию перед загрузкой логотипа</p>';
	}else
	{
	    $this->widget('GalleryManager', array(
	        'gallery' => $model->galleryBehavior->getGallery(),
	        'controllerRoute' => '/admin/gallery'
	    ));
	}*/
	?>

	<?php echo $form->textFieldRow($model, 'order'); ?>
	<?php echo $form->checkBoxRow($model, 'visible'); ?>

	<div class="form-actions">
		<?php $form->widget('bootstrap.widgets.TbButton', array(
			'buttonType' => 'submit',
			'type'  => 'primary',
			'label' => $model->isNewRecord ? 'Создать' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
