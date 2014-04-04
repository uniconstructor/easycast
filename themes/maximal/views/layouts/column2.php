<?php 
/**
 * Верстка страницы на 2 колонки
 */
/* @var $this Controller */

// head-раздел и мета-теги
$this->beginContent('//layouts/main');
?>
<div class="row-fluid">
    <div class="span9">
        <div id="content">
            <?php echo $content; ?>
        </div><!-- content -->
    </div>
    <div class="span3">
        <div id="sidebar">
        <?php
            $this->beginWidget('zii.widgets.CPortlet', array(
                'title' => Yii::t('coreMessages', 'operations'),
            ));
            $this->widget('bootstrap.widgets.TbMenu', array(
                'type'    => 'tabs', 
                'stacked' => true,
                'items'   => $this->menu,
                //'htmlOptions' => array('class' => 'operations'),
            ));
            $this->endWidget();
        ?>
        </div><!-- sidebar -->
    </div>
</div>
<?php
// все скрипты, которые должны быть подключены внизу страницы CClientScript::POS_END
$this->endContent();