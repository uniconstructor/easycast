<?php
/**
 * Страница с сообщением о том что отбор завершен и подать заявку уже нельзя
 */
/* @var $this        VacancyController */
/* @var $vacancy     EventVacancy */
/* @var $questionary Questionary */
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Прием заявок завершен</h1>
                    <h4 class="intro-description">
                        <?php 
                        $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                            'message' => 'Прием заявок на эту роль завершен.',
                        ));
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row text-center">
            <?php 
            // предлагаем посмотреть другие события
            // @todo вывести первые 5 доступных подходящих событий
            $url = Yii::app()->createUrl('//agenda');
            echo CHtml::link('Вернуться к списку событий', $url, array(
                'class' => 'btn btn-large btn-primary',
            ));
            ?>
        </div>
    </div>
</div>