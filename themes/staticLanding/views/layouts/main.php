<?php
/**
 * Основной файл разметки сайта, тема оформления landing page без дополнительных элементов
 */
/* @var $this Controller */

?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Коммерческое предложение проекта easyCast</title>
    
    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/allin_main.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/slider_index.css" rel="stylesheet" type="text/css" />
    
    <link href="<?php echo Yii::app()->baseUrl; ?>/css/helpers.css" rel="stylesheet" type='text/css'>
    
    <!--script src="<?php echo Yii::app()->baseUrl; ?>/js/modernizr.custom.min.js" type="text/javascript"></script-->
</head>
<body>
    <div class="fon-left">  <!-- Картинки слева -->
    <div class="fon-right">	<!-- Картинки справа -->
    
    <?php
    // основное содержимое страницы
    echo $content;
    ?>
    
    </div>
    </div>
</body>
</html> 