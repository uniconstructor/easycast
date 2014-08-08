<?php
/**
 * Страница завершением регистрации
 * 
 * @todo перенаправлять участника сюда после подачи заявки и напоминать ему какую-то дополнительную
 *       информацию на этой странице
 * @todo Выводить здесь список всех активных подходящих ролей на которые идет набор сейчас
 */
/* @var $this VacancyController */
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Заявка принята</h1>
                    <h4 class="intro-description">
                        <?php 
                        $this->widget('bootstrap.widgets.TbAlert');
                        ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row text-center">
            <?php 
            echo CHtml::link('Вернуться к списку событий', '//agenda', array(
                'class' => 'btn btn-large btn-primary',
            ));
            ?>
        </div>
    </div>
</div>