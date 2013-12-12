<?php
/**
 * Сообщение отображаемое после отправки заявки на расчет стоимости
 */

$this->widget('bootstrap.widgets.TbAlert');
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
            'icon'       => 'remove white',
            'url'        => Yii::app()->createAbsoluteUrl('/sire/index'),
        ));
    ?>
    </div>
</div>