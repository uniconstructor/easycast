<?php
/**
 * Страница завершением регистрации
 * 
 * @todo перенаправлять участника сюда после подачи заявки и напоминать ему какую-то дополнительную
 *       информацию на этой странице
 * @todo Выводить здесь список всех активных подходящих ролей на которые идет набор сейчас
 */
/* @var $this VacancyController */
/* @var $redirectUrl string */
/* @var $vacancy EventVacancy */
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Заявка принята</h1>
                        <?php 
                        // FIXME значение в настройку
                        if ( $vacancy->id == 749 )
                        {
                            $redirectUrl = 'http://ctc.ru/rus/projects/show/76527/';
                            $image = CHtml::image('/masterchief/size_3_baner-master-chef-3.jpg', '', array(
                                'style' => 'max-width: 100%;',
                            ));
                            echo CHtml::link($image, $redirectUrl);
                        }
                        ?>
                    <h4 class="intro-description">
                        <?php $this->widget('bootstrap.widgets.TbAlert'); ?>
                    </h4>
                </div>
            </div>
        </div>
        <div class="row text-center">
            <?php 
            // предлагаем посмотреть другие события
            // @todo вывести первые 5 доступных подходящих событий
            //$redirectUrl = Yii::app()->createUrl('//agenda', array('newMode' => 'user'));
            $redirectLabel   = 'Продолжить';
            $redirectOptions = array(
                'class' => 'btn btn-large btn-primary',
            );
            if ( $vacancy->event->project->id == 285 )
            {
                $redirectLabel   = 'Загрузить видео';
                $redirectUrl     = 'http://therealtyshow.ru/one';
                $redirectOptions = array(
                    'class'  => 'btn btn-large btn-info',
                );
                echo '<div class="well">Для продолжения регистрации загрузите видео на сайте therealtyshow.ru</div>';
            }
            echo CHtml::link($redirectLabel, $redirectUrl, $redirectOptions);
            ?>
        </div>
    </div>
</div>
<!--div class="page-alternate">
    <div class="container">
        <div class="row text-center">
            <div class="alert alert-success alert-block">
                Заявка успешно отправлена. Вы можете перейти обратно на сайт проекта.
            </div>
        </div>
        <div class="row text-center">
            <div class="title-page">
                <?php
                //$image = CHtml::image('size_3_baner-master-chef-2.jpg');
                //echo CHtml::link($image, $redirecrUrl);
                ?>
            </div>
        </div>
        <div class="row text-center">
            <?php 
            /*echo CHtml::link('Вернуться на сайт проекта', $redirecrUrl, array(
                'class' => 'btn btn-large btn-primary',
            ));*/
            ?>
        </div>
    </div>
</div-->