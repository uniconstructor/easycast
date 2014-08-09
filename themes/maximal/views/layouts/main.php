<?php
/**
 * Основной файл разметки сайта, landing page
 *
 * @todo перенести подключение сторонних CSS и JS в отдельные плагины
 */
/* @var $this Controller */

// jQuery должен быть на всех страницых темы
Yii::app()->clientScript->registerCoreScript('jquery');

?><!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html class="no-js lt-ie9 lt-ie8" lang="en"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html class="no-js lt-ie9" lang="en"><![endif]-->
<!--[if (IE 9)]><html class="no-js ie9" lang="en"><![endif]-->
<!--[if gt IE 8]><!--> <html lang="ru"> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="ru" />
    <title><?= CHtml::encode($this->pageTitle); ?></title>
    <!-- Mobile Specifics -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="HandheldFriendly" content="true"/>
    <meta name="MobileOptimized" content="320"/> 
    <!-- Bootstrap skin CSS -->
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/bootstrap.min.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/flexslider.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/font-awesome.min.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/main.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type='text/css'>
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/css/responsive.css" rel="stylesheet" type="text/css">
    <link href="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/rs-plugin/css/settings.css" rel="stylesheet" type="text/css">
    <!-- Our helper styles -->
    <link href="<?= Yii::app()->baseUrl; ?>/css/helpers.css" rel="stylesheet" type='text/css'>
    <!-- modernizr lib -->
    <script src="<?= Yii::app()->baseUrl; ?>/js/modernizr.custom.min.js" type="text/javascript"></script>
    <!-- ShareThis widget -->
    <script type="text/javascript">var switchTo5x=true;</script>
    <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
    <script type="text/javascript">stLight.options({publisher: "9144efb6-c5a7-4360-9b70-24e468be66c3", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>
</head>
<body>
    <?php
    // шапка страницы
    $this->widget('ext.ECMarkup.ECResponsiveHeader.ECResponsiveHeader');
    if ( isset($this->breadcrumbs) )
    {// дополнительная навигация
        $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
            'links' => $this->breadcrumbs,
        ));
    }
    // основное содержимое страницы
    echo $content;
    // подвал страницы
    $this->widget('ext.ECMarkup.ECResponsiveFooter.ECResponsiveFooter');
    ?>
    <!-- Bootstrap skin JS -->
    
    <script src="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.isotope.js" type="text/javascript"></script>
    <script src="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/js/placeholder.js" type="text/javascript"></script>
    <script src="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/js/plugins.js" type="text/javascript"></script>
    <!--script src="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/js/theme.js" type="text/javascript"></script-->
    <script src="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/rs-plugin/js/jquery.themepunch.revolution.min.js" type="text/javascript"></script> <!-- Revolution Slider -->
    <script src="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/rs-plugin/pluginsources/jquery.themepunch.plugins.min.js" type="text/javascript"></script> <!-- Revolution Slider -->
    <!--script src="<?= Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.flexslider-min.js" type="text/javascript"></script-->
</body>
</html>