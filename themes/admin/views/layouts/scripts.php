<?php
/**
 * Все скрипты страницы темы оформления: обязательно подключаются в конце страницы, 
 * перед закрывающим "</body>"
 * Если переместите их в заголовок - тема начнет вести себя странно, так что оставьте тут все как есть
 * Это адмика так что гугл-аналитика отключена на всех страницах
 * Не меняйте порядок подключения скриптов. Особенно если вы не читали документацию к админке.
 */
/* @var $this Controller */

// путь к корню темы оформления (там лежат все скрипты и стили)
$themeUrl = Yii::app()->theme->baseUrl.'/';
?>
<!-- END SHORTCUT AREA -->
<!--================================================== -->
<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)
<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>-->
<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<script>
if ( ! window.jQuery ) {
    document.write('<script src="<?= $themeUrl; ?>js/libs/jquery-2.0.2.min.js"></script>');
}
</script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script>
if ( ! window.jQuery.ui ) {
    document.write('<script src="<?= $themeUrl; ?>js/libs/jquery-ui-1.10.3.min.js"></script>');
}
</script>
<!-- JS TOUCH : include this plugin for mobile drag / drop touch events -->
<script src="<?= Yii::app()->theme->baseUrl; ?>js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script>
<!-- BOOTSTRAP JS -->
<script src="<?= $themeUrl; ?>js/bootstrap/bootstrap.min.js"></script>
<!-- CUSTOM NOTIFICATION -->
<script src="<?= $themeUrl; ?>js/notification/SmartNotification.min.js"></script>
<!-- JARVIS WIDGETS -->
<script src="<?= $themeUrl; ?>js/smartwidgets/jarvis.widget.min.js"></script>
<!-- EASY PIE CHARTS -->
<script src="<?= $themeUrl; ?>js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
<!-- SPARKLINES -->
<script src="<?= $themeUrl; ?>js/plugin/sparkline/jquery.sparkline.min.js"></script>
<!-- JQUERY VALIDATE -->
<script src="<?= $themeUrl; ?>js/plugin/jquery-validate/jquery.validate.min.js"></script>
<!-- JQUERY MASKED INPUT -->
<script src="<?= $themeUrl; ?>js/plugin/masked-input/jquery.maskedinput.min.js"></script>
<!-- JQUERY SELECT2 INPUT -->
<script src="<?= $themeUrl; ?>js/plugin/select2/select2.min.js"></script>
<!-- JQUERY UI + Bootstrap Slider -->
<script src="<?= $themeUrl; ?>js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>
<!-- browser msie issue fix -->
<script src="<?= $themeUrl; ?>js/plugin/msie-fix/jquery.mb.browser.min.js"></script>
<!-- FastClick: For mobile devices: you can disable this in app.js -->
<script src="<?= $themeUrl; ?>js/plugin/fastclick/fastclick.js"></script>
<script src="<?= $themeUrl; ?>js/demo.js"></script>
<!-- MAIN APP JS FILE -->
<script src="<?= $themeUrl; ?>js/app.js"></script>