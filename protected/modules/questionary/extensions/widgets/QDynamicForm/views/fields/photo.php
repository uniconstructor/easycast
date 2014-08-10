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
$form->widget('GalleryManager', array(
    'gallery'         => Gallery::model()->findByPk($model->galleryid),
    'controllerRoute' => '//questionary/gallery',
));
//echo $form->error($model, 'galleryid');
echo $form->error($model, 'photo');

// FIXME заменить hardcoded-значение на настройку
if ( $this->vacancy->id === 749 OR YII_DEBUG )
{
?>
    <div style="padding-top: 20px;">
        <h3 class="spec" style="text-transform: none;">
        Для участния в кастинге необходимо загрузить минимум 
        3 фотографии:</h3>
        <ol>
            <li>Ваше портретное фото
            <li>Ваше фото по пояс
            <li>Фотография приготовленного вами на видео блюда
        </ol>
    </div>
<?php
}
