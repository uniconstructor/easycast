<?php
/**
 * Страница для регистрации на стороннем ресурсе (перенаправление трафика)
 */
/* @var $this        VacancyController|InviteController */
/* @var $project     Project */

// запрет индексации поисковиками
Yii::app()->clientScript->registerMetaTag('noindex', 'robots');

if ( $project->hasBanner() )
{// выводим баннер до основного содержимого, чтобы он располагался по во всю ширину страницы
    $bannerImage = CHtml::image($project->getBannerUrl(), CHtml::encode($project->name), array('style' => 'max-width:100%;'));
    echo CHtml::tag('div', array('class' => 'row-fluid text-center'), $bannerImage);
}
?>
<div class="page-alternate">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="title-page">
                    <h1 class="title">Регистрация участия в проекте "<?= strip_tags($project->name); ?>"</h1>
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
                'class'  => 'btn btn-large btn-info',
                'ref'    => 'nofollow',
                'target' => '_blank',
            ));
            ?>
        </div>
    </div>
</div>