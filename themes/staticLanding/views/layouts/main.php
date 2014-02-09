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
    
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/jquery.carouFredSel-6.2.1.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/js/jquery.easing.1.3.js"></script>
    <script type="text/javascript">
    	$(function() {
    		$('.easy_nav a').bind('click',function(event){
    			var $anchor = $(this);
    			
    			$('html, body').stop().animate({
    				scrollTop: $($anchor.attr('href')).offset().top
    			}, 1500,'easeInOutExpo');
    			event.preventDefault();
    		});
    	});
    </script>
    <script type="text/javascript">
    	$(function() {
    		//	Scrolled by user interaction
    		$('#slider_news').carouFredSel({
    			width: 1040,
    			height: 250,
    			auto: false,
    			prev: '#prev',
    			next: '#next'					
    		});	
    	});
    </script>
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