<?php
/**
 * Список полей формы добавления участника в фотовызывной, во всплывающем modal-окне
 */
/* @var $form TbActiveForm */
/* @var $this CallListExternalMembers */
/* @var $model ExternalMemberForm */

// ошибки формы
echo $form->errorSummary(array($model), null, null, array('id' => $this->formId.'_errors'));
// id роли на которую добавляется новый участник
echo CHtml::hiddenField('vacancyid', $this->vacancy->id);
// id фотовызывного, куда добавляется новый участник
echo CHtml::hiddenField('reportid', $this->report->id);
// уникальный хеш участника внутри фотовызывного 
// (чтобы можно было редактировать и удалять добавленных участников)
echo CHtml::hiddenField('hash', $model->hash);

// фотографии: изначально загружаем пустой виджет, его задача - только подключить стили и скрипты
// После того как откроется окно добавления нового участника, пустой виджет галереи заменяется
// точно таким же виджетом, загруженным через AJAX, но уже с только что созданной галереей
// Это сделано для того чтобы не плодить кучу пустых галерей при простой загрузке страницы 
$emptyGallery = new PhotoGallery();
echo $form->labelEx($model, 'galleryid');
echo '<div id="'.$this->formId.'_gallery">';
echo $form->hiddenField($model, 'galleryid');
/*$this->widget('GalleryManager', array(
    'gallery'         => $emptyGallery,
    'controllerRoute' => '/admin/gallery'
));*/
echo '</div>';
echo $form->error($model, 'galleryid');


// фамилия
echo $form->textFieldRow($model, 'lastname');
// имя
echo $form->textFieldRow($model, 'firstname');
// возраст
echo $form->textFieldRow($model, 'age');
// phone
echo $form->textFieldRow($model, 'phone');
// характеристики
echo $form->textFieldRow($model, 'bages');
// комментарий
echo $form->textAreaRow($model, 'comment');


