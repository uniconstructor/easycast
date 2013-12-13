<?php
/**
 * Сообщение отображаемое после отправки заявки на расчет стоимости
 */

$this->widget('bootstrap.widgets.TbAlert');

$this->breadcrumbs = array(
    'Расчет стоимости'
);
?>
<div class="row">
    <div class="span8 offset2" style="text-align: center;">
    <?php 
    $this->widget('bootstrap.widgets.TbButton',
        array(
            'buttonType' => 'link',
            'type'       => 'success',
            'size'       => 'large',
            'label'      => 'Вернуться на главную',
            'url'        => Yii::app()->createAbsoluteUrl('/site/index'),
        ));
    $this->widget('bootstrap.widgets.TbButton',
        array(
            'buttonType' => 'link',
            'type'       => 'default',
            'size'       => 'large',
            'label'      => 'Новый расчет',
            'url'        => Yii::app()->createAbsoluteUrl('/calculation'),
        ));
    ?>
    </div>
</div>