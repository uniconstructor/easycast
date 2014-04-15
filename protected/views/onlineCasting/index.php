<?php
/**
 * Страница приветствия перед началом создания комерческого предложения
 */
/* @var $this OnlineCastingController */

$this->pageTitle = 'Онлайн-кастинг';

$this->breadcrumbs = array(
    'Онлайн-кастинг'
);

?>
<div class="page-alternate">
    <div class="container">
        <div class="title-page">
            <h1 class="title">Онлайн-кастинг</h1>
            <h4 class="intro-description">
                Онлайн-кастинг позволяет вам найти и пригласить людей для вашей съемки прямо на сайте.<br>
                Для того чтобы воспользоваться этим сервисом нужно совершить всего три простых шага.
            </h4>
        </div>
        <div class="row-fluid">
            <div class="span4">
                <div class="box">
                    <div class="icon">
                        <div style="font-weight:normal;padding:0;line-height:98px;height:100px;width:100px;font-size:70px;border-style:solid;border-width:3px;border-radius:50px;">1</div>
                    </div>
                    <h4>Расскажите о своем проекте</h4>
                    <p>Нашим участникам интересно знать куда их хотят пригласить.
                    Чем подробнее описание - тем больше заявок вы получите.</p>
                </div>
            </div>
            <div class="span4">
                <div class="box">
                    <div class="icon">
                        <div style="font-weight:normal;padding:0;line-height:98px;height:100px;width:100px;font-size:70px;border-style:solid;border-width:3px;border-radius:50px;">2</div>
                    </div>
                    <h4>Выберите подходящих людей</h4>
                    <p>Наша система позволяет выполнять поиск по 25 разным параметрам, чтобы вы могли найти 
                    то что вам нужно. Заявки подают только настоящие, живые люди которые действительно хотят 
                    участвовать в вашем проекте.</p>
                </div>
            </div>
            <div class="span4">
                <div class="box">
                    <div class="icon">
                        <div style="font-weight:normal;padding:0;line-height:98px;height:100px;width:100px;font-size:70px;border-style:solid;border-width:3px;border-radius:50px;">3</div>
                    </div>
                    <h4>Пригласите лучших</h4>
                    <p>При отборе заявок вам будет доступна подробная
                    информация по каждому человеку. Мы сделали все, для того чтобы 
                    вам было легче принять решение.</p>
                </div>
            </div>
        </div>
        <div class="row-fluid text-center">
        <?php 
            // кнопка "начать"
            $this->widget('bootstrap.widgets.TbButton',
                array(
                    'buttonType' => 'link',
                    'type'       => 'success',
                    'size'       => 'large',
                    'label'      => 'Начать',
                    'url'        => Yii::app()->createAbsoluteUrl('/onlineCasting/create'),
                    'htmlOptions' => array('style' => 'width:300px;'),
                ));
            ?>
        </div>
    </div>
</div>