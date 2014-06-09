<?php
/**
 * Разметка одного поля анкеты
 * @deprecated оставлено для совместимости, удалить при рефакторинге
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// фотографии
echo $form->labelEx($model, 'galleryid');
echo $form->hiddenField($model, 'galleryid');
$this->widget('GalleryManager', array(
    'gallery'         => Gallery::model()->findByPk($model->galleryid),
    'controllerRoute' => '/questionary/gallery',
));
echo $form->error($model, 'galleryid');