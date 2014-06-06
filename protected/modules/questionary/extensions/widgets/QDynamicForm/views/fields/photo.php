<?php
/**
 * Разметка одного поля анкеты
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// фотографии
echo $form->labelEx($model, 'photos');
echo $form->hiddenField($model, 'galleryid');
$this->widget('GalleryManager', array(
    'gallery'         => $model->gallery,
    'controllerRoute' => '/questionary/gallery',
));
echo $form->error($model, 'galleryid');