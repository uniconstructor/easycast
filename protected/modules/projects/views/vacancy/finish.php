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
            //$redirecrUrl = Yii::app()->createUrl('//agenda', array('newMode' => 'user'));
            echo CHtml::link('Продолжить', $redirectUrl, array(
                'class' => 'btn btn-large btn-primary',
            ));
            // FIXME значение в настройку
            if ( in_array((int)$vacancy->id, array(1017, 1018)) )
            {
                $bannerUrl = 'http://ma.lifestylegroup.ru';
                $image = CHtml::image('https://s3.amazonaws.com/temp.easycast.ru/social/banner.png', '', array(
                    'style' => 'max-width: 100%;height: 120px;',
                ));
                echo '<h4 class="intro-description">Информационная поддержка</h4>';
                echo CHtml::link($image, $bannerUrl);
            }
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