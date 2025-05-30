<?php
/**
 * Разметка одного поля анкеты
 * @deprecated название photo оставлено для совместимости моделью QDynamicFormModel, 
 *             для загрузки фотографий в анкету следует использовать galleryid
 *             а этот фрагмент удалить при рефакторинге
 */
/* @var $form  TbActiveForm */
/* @var $this  QDynamicForm */
/* @var $model QDynamicFormModel */

// фотографии
echo $form->labelEx($model, 'galleryid');
echo $form->hiddenField($model, 'galleryid');
$form->widget('GalleryManager', array(
    'gallery'         => Gallery::model()->findByPk($model->galleryid),
    'controllerRoute' => '//questionary/gallery',
));
//echo $form->error($model, 'galleryid');
echo $form->error($model, 'photo');

// FIXME заменить hardcoded-значение на настройку
if ( $this->vacancy->id == 749 )
{
?>
    <div style="padding-top: 20px;">
        <h3 class="spec" style="text-transform: none;">
            Для участия в кастинге необходимо загрузить минимум 3 фотографии:
        </h3>
        <ol>
            <li>Ваше портретное фото</li>
            <li>Фотография приготовленного вами блюда</li>
        </ol>
    </div>
<?php
}