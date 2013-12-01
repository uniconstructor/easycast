<?php
/**
 * 
 */
/* @var $this QUserMedia */

// создаем сообщение, на случай если ни одной фотографии не загружено
$noPhotoMessage = '<div class="alert">Фотографии не загружены</div>';
if ( $this->questionary->user->id == Yii::app()->user->id )
{// если участник просматривает свою анкету без фотографий - выведем предупреждение
$noPhotoMessage = '<div class="alert alert-danger alert-block">
    <h4 class="alert-heading">Ни одной фотографии не загружено</h4>
            В анкете обязательно должна быть хотя бы одна ваша фотография.
            Без них ваша анкета не будет видна в каталоге или выводиться в поиске.</div>';
}
// выводим предупреждения, если они есть
$this->widget('bootstrap.widgets.TbAlert');