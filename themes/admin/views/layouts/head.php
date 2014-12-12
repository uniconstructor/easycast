<?php
/**
 * Заголовок страницы, стили скрипты и мета-теги
 * 
 * Справка по правильной настройки иконок
 * @see https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html
 * 
 * @todo подключение jGrowl вынести в отдельный виджет 
 * @todo подключение bootstrap вынести в пакеты скриптов
 */
/* @var $this Controller */

// @todo все meta-теги брать из контроллера
// <!-- meta content="" name="description" -->
// <!-- meta content="" name="author" -->

// путь к корню темы оформления (там лежат все скрипты и стили)
$themeUrl = Yii::app()->theme->baseUrl.'/assets/';
?>
<!-- [BEGIN HEAD_INFO] -->
<head>
    <meta charset="utf-8">
    <!--<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">-->
    <title><?= CHtml::encode($this->pageTitle); ?></title>
    
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    
    <!-- #CSS Links -->
    <!-- Basic Styles -->
    <link href="<?= $themeUrl; ?>css/bootstrap.min.css" media="screen" rel="stylesheet" type="text/css">
    <link href="<?= $themeUrl; ?>css/font-awesome.min.css" media="screen" rel="stylesheet" type="text/css">
    
    <!-- jGrowl notifications -->
    <link href="<?= Yii::app()->baseUrl; ?>/css/jquery.jgrowl.min.css" rel="stylesheet" type="text/css">
    
    <!-- SmartAdmin Styles : Caution! DO NOT change the order -->
	<link href="<?= $themeUrl; ?>css/smartadmin-production-plugins.min.css" rel="stylesheet" type="text/css" media="screen" >
	<link href="<?= $themeUrl; ?>css/smartadmin-production.min.css" rel="stylesheet" type="text/css" media="screen">
	<link href="<?= $themeUrl; ?>css/smartadmin-skins.min.css" rel="stylesheet" type="text/css" media="screen">
	
	<!-- easyCast Styles-->
	<link rel="stylesheet" type="text/css" media="screen" href="css/easycast.css">
    
    <!-- #FAVICONS -->
    <link href="<?= $themeUrl; ?>img/favicon/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="<?= $themeUrl; ?>img/favicon/favicon.ico" rel="icon" type="image/x-icon">
    
    <!-- #GOOGLE FONT -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700" rel="stylesheet">
    
    <!-- #APP SCREEN / ICONS -->
    <!-- Specifying a Webpage Icon for Web Clip -->
    <link href="<?= $themeUrl; ?>img/splash/sptouch-icon-iphone.png" rel="apple-touch-icon">
    <link href="<?= $themeUrl; ?>img/splash/touch-icon-ipad.png" rel="apple-touch-icon" sizes="76x76">
    <link href="<?= $themeUrl; ?>img/splash/touch-icon-iphone-retina.png" rel="apple-touch-icon" sizes="120x120">
    <link href="<?= $themeUrl; ?>img/splash/touch-icon-ipad-retina.png" rel="apple-touch-icon" sizes="152x152">
    
    <!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    
    <!-- Startup image for web apps -->
    <link href="<?= $themeUrl; ?>img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)" rel="apple-touch-startup-image">
    <link href="<?= $themeUrl; ?>img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)" rel="apple-touch-startup-image">
    <link href="<?= $themeUrl; ?>img/splash/iphone.png" media="screen and (max-device-width: 320px)" rel="apple-touch-startup-image">
</head>
<!-- [END HEAD_INFO] -->