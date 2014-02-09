<?php
/**
 * Основной файл разметки сайта, landing page
 *
 * @todo перенести подключение сторонних CSS и JS в отдельные плагины
 */
/* @var $this Controller */

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="language" content="ru" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
<!-- HTML5: for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- modernizr lib -->
<script src="<?php echo Yii::app()->baseUrl; ?>/js/modernizr.custom.min.js" type="text/javascript"></script>

<link href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/css/bootstrap.css" rel="stylesheet" type='text/css'>
<link href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/css/pricing.css" rel="stylesheet" type='text/css'>
<link href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" type='text/css'>
<link href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/css/style.css" rel="stylesheet" type='text/css'>
<link href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/css/font-awesome.min.css" rel="stylesheet" type='text/css'>
<link href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/css/prettyPhoto.css" rel="stylesheet" type="text/css">
<link href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/google-code-prettify/prettify.css" rel="stylesheet" type='text/css'>

<link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/landing.css" rel="stylesheet" type='text/css'>
<link href="<?php echo Yii::app()->theme->baseUrl; ?>/css/cbpFWSlider.css" rel="stylesheet" type='text/css'>

<link href="<?php echo Yii::app()->baseUrl; ?>/css/helpers.css" rel="stylesheet" type='text/css'>
<link
    href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic&amp;subset=latin,latin-ext'
    rel='stylesheet' type='text/css'>

<link rel="apple-touch-icon-precomposed" sizes="144x144"
    href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/ico/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114"
    href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/ico/apple-touch-icon-114-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="72x72"
    href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/ico/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/ico/apple-touch-icon-57-precomposed.png">
</head>
<body data-spy="scroll" data-target=".scroller-spy" data-twttr-rendered="false">
    <!--START MAIN-WRAPPER-->
    <div class="main-wrapper">
	<?php
    // @todo шапка страницы
    
    // основное содержимое страницы
    echo $content;
    ?>
    </div>
    <!-- END: MAIN-WRAPPER-->

    <!--javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/google-code-prettify/prettify.js" type="text/javascript"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.prettyPhoto.js" type="text/javascript"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/tweetable.jquery.js" type="text/javascript"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.timeago.js" type="text/javascript"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.fitvids.min.js" type="text/javascript"></script>

    <!-- PARALLAX PLUGIN -->
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.localscroll-1.2.7-min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.inview.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.scrollTo-1.4.2-min.js"></script>
    <script type="text/javascript" src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/jquery.parallax-1.1.3.js"></script>
    <!-- PARALLAX PLUGIN -->


    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/bootstrap/js/custom.js" type="text/javascript"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/jquery.lettering.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl; ?>/js/jquery.cbpFWSlider.min.js" type="text/javascript"></script>

    <script type="text/javascript">
    jQuery(document).ready(function(){
    	jQuery('#nav').localScroll(1000);
    	jQuery('#nav2').localScroll(1000);
    	jQuery('#nav3').localScroll(1000);
    	jQuery('#nav4').localScroll(1000);
    	jQuery('#nav5').localScroll(1000);
    	//.parallax(xPosition, speedFactor, outerHeight) options:
    	//xPosition - Horizontal position of the element
    	//inertia - speed to move relative to vertical scroll. Example: 0.1 is one tenth the speed of scrolling, 2 is twice the speed of scrolling
    	//outerHeight (true/false) - Whether or not jQuery should use it's outerHeight option to determine when a section is in the viewport
    	//jQuery('#main-wrapper').parallax("50%", 0.1);
    	jQuery('#header-section').parallax("50%", 0.1);
    	jQuery('#services-section').parallax("50%", 0.1);
    	jQuery('#projects-section').parallax("50%", 0.1);
    	jQuery('.bg').parallax("50%", 0.3);
    	//jQuery('#portfolio-section').parallax("50%", 0.3);
    	//jQuery('#price-section').parallax("50%", 0.3);
    	//jQuery('#contact-section').parallax("50%", 0.1);
    	//jQuery('#slogan-section-1').parallax("50%", 0.3);
    })
    </script>


    <script>
	var navbar = jQuery('#navbartop');
	var navbartop = jQuery('#topnavbar');
		jQuery(window).scroll(function () {
		    navbar.addClass("navbar-scroll");
			navbartop.removeClass("navbar-static-top");
			navbartop.addClass("navbar-fixed-top");
			/*if ( jQuery(this).scrollTop() > 640) {
				navbar.addClass("navbar-scroll");
				navbartop.removeClass("navbar-static-top");
				navbartop.addClass("navbar-fixed-top");
			} else {
				navbar.removeClass("navbar-scroll");
				navbartop.removeClass("navbar-fixed-top");
				navbartop.addClass("navbar-static-top");
			}*/
		});
	</script>

    <script>
        $(".container").fitVids();  
		jQuery('.carousel').carousel();
		/*$(function() {
			$("#cd-photo-letter-container h2 a").lettering();
		});*/
	</script>
	
	<script>
		$( function() {
			/*
			- how to call the plugin:
			$( selector ).cbpFWSlider( [options] );
			- options:
			{
				// default transition speed (ms)
				speed : 500,
				// default transition easing
				easing : 'ease'
			}
			- destroy:
			$( selector ).cbpFWSlider( 'destroy' );
			*/

			$( '.cbp-fwslider' ).cbpFWSlider();

		} );
	</script>
</body>
</html>