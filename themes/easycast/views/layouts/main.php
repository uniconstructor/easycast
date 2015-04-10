<?php
/**
 * Основной шаблон разметки
 * 
 * @todo поиск
 * @todo вход и регистрация для гостей
 */
/* @var $content string */

// путь к корню темы оформления (там лежат все скрипты и стили)
$themeUrl = Yii::app()->theme->baseUrl.'/assets/';

?><!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= CHtml::encode($this->pageTitle); ?> - EasyCast</title>
        <!-- Bootstrap -->
        <link href="<?= $themeUrl; ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" rel="stylesheet" media="screen">    
        <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400,300,700&subset=cyrillic,latin' rel='stylesheet' type='text/css'>
        <link href="<?= $themeUrl; ?>css/custom/icheck/minimal/minimal.css" rel="stylesheet">
        <link href="<?= $themeUrl; ?>css/custom/global.css" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="shortcut icon" href="<?= $themeUrl; ?>images/favicon.ico" type="image/x-icon">
    </head>
    <body>
        <div class="container">
            <header class="row">
                <div id="nav-top">
                    <div class="pull-left">
                        <div id="logo"><a class="noborder" href="#"><img src="<?= $themeUrl; ?>images/logo.png"/></a></div>
                    </div>
                    <div class="pull-right">
                        <!--div id="search">
                            <form action="" method="GET">    
                                <input id="nav-search" value="Поиск по сайту" required="required" onfocus="this.value = ''" onblur="if (this.value == '') {this.value = 'Поиск по сайту';}" class="form-control" name="Search[QUERY]" type="text">    
                                <input type="submit" name="yt2" value="">    
                            </form>
                        </div-->
                        <div id="user">
                            <a class="noborder"><img class="avatar img-circle" src="<?= Yii::app()->user->getAvatarUrl(); ?>"/></a>
                            <a class="name" href="<?= Yii::app()->user->getProfileUrl(); ?>"><?= Yii::app()->user->getFullName(); ?></a>
                            <a class="noborder" href="/logout"><img src="<?= $themeUrl; ?>images/i-logout.png"/></a>
                        </div>
                    </div>
                </div>
                <div id="nav-menu">
                    <ul>
                        <li><a href="">Главная</a></li>
                        <li><a href="">Проекты</a></li>
                        <li><a href="">Мероприятия</a></li>
                        <!--li  class="active"><a href="">FAQ</a></li-->
                        <!--li><a href="">Контакты</a></li-->
                    </ul>
                </div>
            </header>
            <?php echo $content; ?>
            <footer class="row">
                <div class="col-md-3">
                    <img src="<?= $themeUrl; ?>images/logo-white.png" />
                    <p class="copyright">© 2005-<?= date('Y') ?>КАСТИНГОВОЕ АГЕНТСТВО «EASYCAST». ВСЕ ПРАВА ЗАЩИЩЕНЫ.</p>
                </div>
                <div class="col-md-3">
                    <ul>
                        <li><a href="#">Проекты</a></li>
                        <li><a href="#">Мероприятия</a></li>
                        <!--li><a href="#">Связаться с нами</a></li-->
                    </ul>
                </div>
                <div class="col-md-3">
                    <ul>
                        <!--li><a href="#">Контакты</a></li>
                        <li><a href="#">Как это работает</a></li>
                        <li><a href="#">Связаться с нами</a></li-->
                    </ul>
                </div>
                <div class="col-md-3">
                    <div id="dev">
                        <img src="<?= $themeUrl; ?>images/logolance.png" alt="Puertolance Web Studio"/>
                        Разработка дизайна
                        <a href="#" nofollow>Puertolance</a>
                    </div>
                </div>
            </footer>
            <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
            <!-- Include all compiled plugins (below), or include individual files as needed -->
            <script src="<?= $themeUrl; ?>js/bootstrap.min.js"></script>
            <script src="<?= $themeUrl; ?>js/custom/icheck.min.js"></script>
            <script src="<?= $themeUrl; ?>js/custom/imagesloaded.pkgd.min.js"></script>
            <script src="<?= $themeUrl; ?>js/custom/positions.js"></script>
            <script src="<?= $themeUrl; ?>js/custom/script.js"></script>
        </div>
    </body>
</html>