<?php
/**
 * Страница выбора участник/заказчик
 * @todo языковые строки
 */
/* @var $this SiteController */
/*
 * Уникальный сервис - Мы единственная кастинг компания в России, которая предлагает вам роли исключительно подходящие под ваши критерии и условия.
Множество предложений - Ежегодно мы утверждаем артистов на более, чем 15000 ролей
Совр тех
БЕСПЛАТНО - Все наши услуги для вас абсолютно бесплатно, сейчас и в дальнейшем. [ Попробуйте! ]
 */


$customerUrl = Yii::app()->createUrl('//sale', array('newState' => 'customer'));
$customerLinkOptions = array(
    //'data-toggle' => 'tooltip',
    //'data-title'  => 'Войти как заказчик',
    'title'  => 'Заказчикам',
);
$customerLink = CHtml::link('Заказчикам', $customerUrl, $customerLinkOptions);

$userUrl = Yii::app()->createUrl('//index', array('newState' => 'user'));
$userLinkOptions = array(
    //'data-toggle' => 'tooltip',
    //'data-title'  => 'Войти как участник',
    'title'  => 'Участникам',
);
$userLink = CHtml::link('Участникам', $userUrl, $userLinkOptions);

?>
<div class="page-alternate">
    <div class="row-fluid">
        <div class="span6 page">
            <div class="title-page">
                <h2 class="title"><?= $customerLink; ?></h2>
                <h4 class="intro-description">
                    Мы помогаем найти, выбрать и пригласить актеров
                </h4>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <a class="box" href="<?= $customerUrl; ?>">
                        <div class="icon">
                            <i class="icon-thumbs-up"></i>
                        </div>
                        <h4>10 лет опыта</h4>
                        <p>Кастинговое агентство easyCast успешно работает с 2004 года.</p>
                    </a>
                </div>
                <div class="span6">
                    <a class="box" href="<?= $customerUrl; ?>">
                        <div class="icon">
                            <i class="icon-star"></i>
                        </div>
                        <h4>Подбираем любых артистов</h4>
                        <p>Актеры всех категорий, модели, артисты циркового жанра, всевозможные типажи, групповка и массовка.</p>
                    </a>
                </div>
            </div>
            <div class="row-fluid">
                <div class="span6">
                    <a class="box" href="<?= $customerUrl; ?>">
                        <div class="icon">
                            <i class="icon-film"></i>
                        </div>
                        <h4>Для любых съемок</h4>
                        <p>Обслуживаем фильмы, рекламу, сериалы, телепроекты, клипы и другие съемки.</p>
                    </a>
                </div>
                <div class="span6">
                    <a class="box" href="<?= $customerUrl; ?>">
                        <div class="icon">
                            <i class="icon-tablet"></i>
                        </div>
                        <h4>Используем современные технологии</h4>
                        <p>Мы создали мощнейшие инструменты кастинга и автоматизировали 80% своей работы.</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="span6" style="padding:70px 0;">
            <div class="title-page">
                <h2 class="title"><?= $userLink; ?></h2>
                <h4 class="intro-description">
                    Мы предлагаем участие в съемках
                </h4>
            </div>
            <div class="row-fluid">
                <div class="row-fluid">
                    <div class="span6">
                        <a class="box" href="<?= $userUrl; ?>">
                            <div class="icon">
                                <i class="icon-trophy"></i>
                            </div>
                            <h4>Уникальный сервис</h4>
                            <p>Мы единственная кастинг компания в России, которая предлагает 
                            вам роли исключительно подходящие под ваши критерии и условия.</p>
                        </a>
                    </div>
                    <div class="span6">
                        <a class="box" href="<?= $userUrl; ?>">
                            <div class="icon">
                                <i class="icon-list-ol"></i>
                            </div>
                            <h4>Множество предложений</h4>
                            <p>За год мы утверждаем около <b>15.000</b> артистов на различные 
                            роли: от эпизодических до главных.</p>
                        </a>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <a class="box" href="<?= $userUrl; ?>">
                            <div class="icon">
                                <i class="icon-magic"></i>
                            </div>
                            <h4>Удобно</h4>
                            <p>Подать заявку на роль можно в один клик.</p>
                        </a>
                    </div>
                    <div class="span6">
                        <a class="box" href="<?= $userUrl; ?>">
                            <div class="icon">
                                <i class="icon-smile"></i>
                            </div>
                            <h4>Бесплатно</h4>
                            <p>Никакой платы за регистрацию или пользование.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid text-center">
        <div class="span6" style="background: none repeat scroll 0 0 #F5F5F5;">
            <a href="<?= $customerUrl; ?>" class="btn btn-large btn-primary">Подробнее</a>
        </div>
        <div class="span6">
            <a href="<?= $userUrl; ?>" class="btn btn-large btn-success">К списку событий</a>
        </div>
    </div>
</div>
<div class="page-alternate">
    <?php
    // все текущие события 
    $dateCriteria = new CDbCriteria();
    $dateCriteria->scopes = array(
        'withDate',
        'startsAfterNow',
    );
    $this->widget('projects.extensions.EventsAgenda.EventsAgenda', array(
        'displayActive' => true,
        'criteria'      => $dateCriteria,
        'header'        => 'Наши события',
        'title'         => 'На эти события в данный момент идет набор.',
    ));
    // все события без определенной даты выводятся в самом конце
    $noDateCriteria = new CDbCriteria();
    $noDateCriteria->scopes = array(
        'withoutDate',
        // если событие без конкретной даты - то для отображения в календаре 
        // в нем должна быть хотя бы 1 роль на которую идет набор 
        'hasActiveVacancies',
    );
    $this->widget('projects.extensions.EventsAgenda.EventsAgenda', array(
        'displayActive'   => true,
        'displayFinished' => false,
        'criteria'        => $noDateCriteria,
        'header'          => 'Дата уточняется',
        'title'           => 'Набор на эти проекты уже открыт, но точная дата съемок пока неизвестна.',
    ));
    ?>
</div>