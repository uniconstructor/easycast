<?php
/**
 * Слайдер с командой
 */
/* @var $this SaleController */

$members = array();
$data    = array(
    array(
        'image'    => Yii::app()->theme->baseUrl.'/images/photo1.png',
        'name'     => 'Николай Гришин',
        'position' => 'управляющий партнер',
        'phone'    => '+7 926 782 70 87',
        'email'    => 'ceo@easycast.ru',
        'facebook' => 'facebook.com/ngrishinru',
    ),
    array(
        'image'    => Yii::app()->theme->baseUrl.'/images/elarsen.png',
        'name'     => 'Елизавета Ларсен',
        'position' => 'руководитель проектов',
        'phone'    => '+7 967 052 20 25',
        'email'    => 'liza@easycast.ru',
        'facebook' => 'facebook.com/larsen.liza',
    ),
    array(
        'image'    => Yii::app()->theme->baseUrl.'/images/ibyzaeva.png',
        'name'     => 'Ирина Бузаева',
        'position' => 'руководитель проектов',
        'phone'    => '+7 915 066 86 05',
        'email'    => 'irina@easycast.ru',
        'facebook' => 'facebook.com/buzaevairina',
    ),
    array(
        'image'    => Yii::app()->theme->baseUrl.'/images/mkorolev.png',
        'name'     => 'Максим Королев',
        'position' => 'руководитель проектов',
        'phone'    => '+7 926 786 86 64',
        'email'    => 'max@easycast.ru',
        'facebook' => 'facebook.com/maxim.korolev.712',
    ),
);

foreach ( $data as $item )
{
    $members[] = $this->renderPartial('_member', array('member' => $item), true);
}

$this->widget('ext.ECMarkup.ECObjectSlider.ECObjectSlider', array(
    'objects' => $members,
));