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
    
    <script src="<?php echo Yii::app()->baseUrl; ?>/js/modernizr.custom.min.js" type="text/javascript"></script>
</head>
<body>
    <div class="fon-left"><!-- Картинки слева -->
        <div class="fon-right"><!-- Картинки справа -->
        <?php
        // основное содержимое страницы
        echo $content;
        ?>
        </div>
    </div>
<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//v2.zopim.com/?1jwtx0mz7NF3bfar8I0vxNEn7iLRzlDC';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
<?php 
// Подставляем имя и email заказчика в онлайн-чат, если он зашел по ссылке из письма
if ( $offer = Yii::app()->session->get('activeOffer') )
{/* @var $offer CustomerOffer */
    echo "\$zopim(function() {";
    if ( $offer->email )
    {
        echo "\$zopim.livechat.setEmail('{$offer->email}');";
    }
    if ( $offer->name )
    {
        echo "\$zopim.livechat.setName('{$offer->name}');";
    }
    echo "});";
}
?>
</script>
<!--End of Zopim Live Chat Script-->
</body>
</html> 