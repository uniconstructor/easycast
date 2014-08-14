<?php
/**
 * Страница отображаемая вместо анкеты если анкета пользователя скрыта
 * @todo добавить форму входа на сайт
 */
/* @var $this QuestionaryController */
/* @var $questionary Questionary */

$this->pageTitle = 'Страница скрыта (easyCast)';
// ссылка на страницу с онлайн-кастингом
$castingLink = CHtml::link('форму онлайн-кастинга', Yii::app()->createUrl('//onlineCasting'));
?>
<section>
    <div class="page-alternate">
        <div class="container">
            <div class="title-page">
                <h1>Страница скрыта</h1>
                <h4 class="title-description">
                    Этот участник предпочел убрать свои данные из поиска.
                    Если вы хотите найти людей для съемки - то предлагаем вам заполнить 
                    <?= $castingLink ?> на нашем сайте, и мы подберем для вас нужных артистов.
                </h4>
            </div>
        </div>
    </div>
</section>