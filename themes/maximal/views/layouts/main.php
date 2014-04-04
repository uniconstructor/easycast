<?php
/**
 * Основной файл разметки сайта, landing page
 *
 * @todo перенести подключение сторонних CSS и JS в отдельные плагины
 */
/* @var $this Controller */

?><!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if (IE 9)]><html class="no-js ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><![endif]-->
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="ru" />
    <title><?= CHtml::encode($this->pageTitle); ?></title>
    <!-- Mobile Specifics -->
    <!--meta name="viewport" content="width=device-width, initial-scale=1.0"-->
    <meta name="HandheldFriendly" content="true"/>
    <meta name="MobileOptimized" content="320"/> 
    
    <!-- Bootstrap skin CSS -->
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/flexslider.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/font-awesome.min.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/font-awesome.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/main.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/responsive.css" rel="stylesheet" type="text/css">
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/rs-plugin/css/settings.css" rel="stylesheet" type="text/css">
    
    <!-- Our helper styles -->
    <link href="<?= Yii::app()->baseUrl; ?>/css/helpers.css" rel="stylesheet" type='text/css'>
    <!-- modernizr lib -->
    <script src="<?= Yii::app()->baseUrl; ?>/js/modernizr.custom.min.js" type="text/javascript"></script>
</head>
<body>
<?php
// @todo шапка страницы
$this->widget('ext.ECMarkup.ECResponsiveHeader.ECResponsiveHeader');
// основное содержимое страницы
echo $content;
?>
<!-- Bootstrap skin JS -->
<script src="<?= Yii::app()->theme->baseUrl; ?>/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= Yii::app()->theme->baseUrl; ?>/js/jquery.flexslider-min.js" type="text/javascript"></script>
<script src="<?= Yii::app()->theme->baseUrl; ?>/js/jquery.isotope.js" type="text/javascript"></script>
<script src="<?= Yii::app()->theme->baseUrl; ?>/js/placeholder.js" type="text/javascript"></script>
<script src="<?= Yii::app()->theme->baseUrl; ?>/js/plugins.js" type="text/javascript"></script>
<script src="<?= Yii::app()->theme->baseUrl; ?>/js/theme.js" type="text/javascript"></script>
</body>
</html>