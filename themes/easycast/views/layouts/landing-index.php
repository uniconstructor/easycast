<?php
/**
 * Разметка главной страницы (landing)
 * 
 * @todo заменить облачные cdn на собственные assets для надежности 
 * @todo перенести скрипты внутрь сегментов для целостности
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
        <title>EasyCast</title>
        <!-- Bootstrap -->
        <link href="<?= $themeUrl; ?>css/bootstrap.min.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400,300,700&subset=cyrillic,latin' rel='stylesheet' type='text/css'>
        <link href="<?= $themeUrl; ?>css/custom/icheck/minimal/minimal.css" rel="stylesheet">
        <link href="<?= $themeUrl; ?>css/custom/global.css" rel="stylesheet">
        <link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" rel="stylesheet" media="screen">    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/css/swiper.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/css/swiper.min.css">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    </head>
    <body id="index">
        <?php 
        echo $content;
        ?>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?= $themeUrl; ?>js/bootstrap.min.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/icheck.min.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/imagesloaded.pkgd.min.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/positions.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/script.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/dots/TweenLite.min.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/dots/EasePack.min.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/dots/rAF.js"></script>
        <script src="<?= $themeUrl; ?>js/custom/dots/demo-1.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.jquery.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.0.6/js/swiper.jquery.min.js"></script>

        <script>
        $(document).ready(function(){
           // cache the window object
           $window = $(window);

           $('section[data-type="background"]').each(function(){
             // declare the variable to affect the defined data-type
             var $scroll = $(this);

              $(window).scroll(function() {
                // HTML5 proves useful for helping with creating JS functions!
                // also, negative value because we're scrolling upwards                             
                var yPos = -($window.scrollTop() / $scroll.data('speed')); 

                // background position
                var coords = '50% '+ yPos + 'px';

                // move the background
                $scroll.css({ backgroundPosition: coords });    
              }); // end window scroll
           });  // end section function
        }); // close out script
        </script>

        <script>
        var swiper = new Swiper('.projects-swiper-container', {
            effect: 'coverflow',
            initialSlide : 3,
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: 'auto',
            coverflow: {
                rotate: 30,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows : false
            }
        });
        </script>
        <script>
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                initialSlide : 1,
                effect: 'coverflow',
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: 'auto',
                coverflow: {
                    rotate: 50,
                    stretch: 0,
                    depth: 100,
                    modifier: 1,
                    slideShadows : true
                }
            });
        </script>
        <script>
            function sdf_FTS(_number,_decimal,_separator)
            {
                var decimal=(typeof(_decimal)!='undefined')?_decimal:2;
                var separator=(typeof(_separator)!='undefined')?_separator:'';
                var r=parseFloat(_number)
                var exp10=Math.pow(10,decimal);
                r=Math.round(r*exp10)/exp10;
                rr=Number(r).toFixed(decimal).toString().split('.');
                b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
                r=(rr[1]?b+'.'+rr[1]:b);

                return r;
            }
            setTimeout(function(){
                    $('#counter').text('0');
                    $('#counter1').text('0');
                    $('#counter2').text('0');
                    setInterval(function(){

                        var curval=parseInt($('#counter').text());
                        var curval1=parseInt($('#counter1').text().replace(' ',''));
                        var curval2=parseInt($('#counter2').text());
                        if(curval<=707){
                            $('#counter').text(curval+1);
                        }
                        if(curval1<=12280){
                            $('#counter1').text(sdf_FTS((curval1+20),0,' '));
                        }
                        if(curval2<=245){
                            $('#counter2').text(curval2+1);
                        }
                    }, 2);

                }, 500);
        </script>
    </body>
</html>