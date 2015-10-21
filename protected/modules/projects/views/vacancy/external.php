<?php
/**
 * Страница для регистрации на стороннем ресурсе (перенаправление трафика)
 */
/* @var $this        VacancyController */
/* @var $vacancy     EventVacancy */
/* @var $questionary Questionary */

// запрет индексации поисковиками
Yii::app()->clientScript->registerMetaTag('noindex', 'robots');
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Регистрация заявки через официальный сайт проекта</h1>
                    <h4 class="intro-description">
                        <?php 
                        $this->widget('ext.ECMarkup.ECAlert.ECAlert', array(
                            'message' => 'Заявки на эту роль принимаются только на официальном сайте проекта.<br>' .
                                'Для продолжения нажмите кнопку "Подать заявку".<br>'.
                                '(Форма регистрации откроется в новом окне)',
                        ));
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row text-center">
            <?php 
            // ссылка на сторонний ресурс (в новом окне)
            echo CHtml::link('Подать заявку', $externalUrl, array(
                'class' => 'btn btn-large btn-info',
                'ref'   => 'nofollow',
            ));
            ?>
        </div>
    </div>
</div>