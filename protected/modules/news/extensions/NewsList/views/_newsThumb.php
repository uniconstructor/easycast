<?php
/**
 * Отображение одного элемента в списке новостей
 * 
 * @todo поправить семантику в соответствии с HTML5
 */

$url = Yii::app()->createUrl('/news/news/view', array('id' => $data->id));
?>
<hr>
<h2><?php echo CHtml::link(CHtml::encode($data->name), $url); ?></h2>
<p><?php echo $data->description; ?></p>
<small class="pull-right muted"><i><?php echo date('Y/m/d', $data->timecreated); ?></i></small>
<br>
<?
    // кнопка "подробнее"
    echo CHtml::link(Yii::t('coreMessages', 'read_more'), $url, array('class' => 'btn btn-primary'));
?>
