<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name . ' - Наши услуги';
$this->breadcrumbs=array(
	'Наши услуги',
);
?>



<div class="row" style="margin-bottom:30px;">
    <div class="span12">
        Кастинговое агенство EasyCast успешно обеспечивает лицами самые рейтинговые, сложные и масштабные съемки
        фильмов, сериалов и телепроектов, начиная с 2005 года. <br>
        Наша команда приятно удивит вас высоким уровнем ответственности и профессионализма! <br>
        В начале 2013 года наша компания крупнейший в России и первый полностью автоматизированный ресурс по
        поиску актеров, моделей и статистов.
    </div>
</div>
<div class="row" style="margin-bottom:30px;">
    <div class="span12">
        <div class="span6">
            <h2>Наши услуги</h2>
            <ul style="list-style: square;">
                <li>Подбор актеров первого и второго плана</li>
                <li>Поиск участников телепроектов</li>
                <li>Типажи любой сложности и статисты</li>
                <li>Проведение кастингов в кратчайшие сроки</li>
                <li>Артисты массовых сцен всех категорий</li>
                <li>Организация флешмобов</li>
            </ul>
        </div>
        <div class="span5">
            <?php echo CHtml::image(Yii::app()->createAbsoluteUrl('//images/services1-small.png'));  ?>
        </div>
    </div>
</div>
<div class="row" style="margin-bottom:30px;">
    <div class="span6">
        <?php echo CHtml::image(Yii::app()->createAbsoluteUrl('//images/services2-small.png'));  ?>
    </div>
    <div class="span6">
        <h2>Наши приоритеты</h2>
        <ul style="list-style: square;">
            <li>Качество предоставляемых услуг</li>
            <li>Точность в реализации задач</li>
            <li>Ответственность за результат</li>
            <li>Надежность в каждой детали</li>
            <li>Организация высочайшено уровня</li>
        </ul>
    </div>
</div>
<div class="row">
    <div class="span4" style="padding: 10px; text-align: center;">
    <?php // кнопка срочного заказа
        $this->widget('bootstrap.widgets.TbButton', array(
            'label'=>'Сделать заказ',
            'type'=>'primary',
            'size'=>'large',
            'htmlOptions' => array(
                'data-toggle' => 'modal',
                'data-target' => "#fastOrderModal"),
        ));
    ?>
        <div style="text-align: left;">
        <?php 
            // скрипт формы срочного заказа
            $this->widget('ext.ECMarkup.ECFastOrder.ECFastOrder');
        ?>
        </div>
    </div>
    <div class="span3" style="padding: 10px; text-align: center;"><h3>mail@easycast.ru</h3></div>
    <div class="span4" style="padding: 10px; text-align: center;">
    <?php 
        // Кнопка обратной связи
        // @todo сделать форму во всплывающем окне как и срочный заказ
        $this->widget('bootstrap.widgets.TbButton', array(
            'label'=>'Обратная связь',
            'type'=>'primary',
            'size'=>'large',
            'url' => Yii::app()->createAbsoluteUrl('/site/contact'),
        ));
    ?>
    </div>
</div>