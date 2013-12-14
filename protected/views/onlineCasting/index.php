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
<div class="row">
    <div class="span11 offset1">
        <div class="alert alert-info alert-block">
            Онлайн-кастинг позволяет вам найти и пригласить людей для вашей съемки прямо на сайте.<br>
            Для того чтобы воспользоваться этим сервисом нужно всего три простых шага.
        </div>
    </div>
</div>
<div class="row">
    <div class="span11 offset1">
        <div class="row row-fluid">
            <div class="span4">
                <h3>Расскажите о своем проекте</h3>
                <p>Нашим участникам интересно знать куда их хотят пригласить.
                Чем подробнее описание - тем больше заявок вы получите.</p>
            </div>
            <div class="span4">
                <h3>Выберите подходящих людей</h3>
                <p>Наша система позволяет выполнять поиск по 25 разным параметрам, чтобы вы могли найти 
                то что вам нужно. Заявки подают только настоящие, живые люди которые действительно хотят 
                участвовать в вашем проекте.</p>
            </div>
            <div class="span4">
                <h3>Пригласите лучших</h3>
                <p>При отборе заявок вам будет доступна подробная
                информация по каждому человеку. Мы сделали все, для того чтобы 
                вам было легче принять решение.</p>
            </div>
        </div>
    </div>
</div>
<div class="row" style="text-align:center;">
<?php 
    // кнопка "начать"
    $this->widget('bootstrap.widgets.TbButton',
        array(
            'buttonType' => 'link',
            'type'       => 'success',
            'size'       => 'large',
            'label'      => 'Начать',
            'url'        => Yii::app()->createAbsoluteUrl('/onlineCasting/create'),
        ));
    ?>
</div>