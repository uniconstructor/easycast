<?php
/**
 * Все скрипты страницы темы оформления: обязательно подключаются в конце страницы, 
 * перед закрывающим "</body>"
 * Если переместите их в заголовок - тема начнет вести себя странно, так что оставьте тут все как есть
 * Это адмика так что гугл-аналитика отключена на всех страницах
 * Не меняйте порядок подключения скриптов без чтения документации к админки
 * 
 * @todo протестировать, насколько много памяти потребляется PACE LOADER после обновления до 1.5.2
 * @todo подключить FastClick для мобильных устройств
 */
/* @var $this Controller */

// путь к корню темы оформления (там лежат все скрипты и стили)
$themeUrl = Yii::app()->theme->baseUrl.'/assets/';

// временно отключенные библиотеки
// 
?>
<!--================================================== -->
<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices) -->
<script data-pace-options='{ "restartOnRequestAfter": true }' src="<?= $themeUrl; ?>js/plugin/pace/pace.min.js"></script>

<!-- jQuery + jQueryUI -->
<script src="<?= $themeUrl; ?>js/libs/jquery-2.1.1.min.js"></script>
<script src="<?= $themeUrl; ?>js/libs/jquery-ui-1.10.3.min.js"></script>

<!-- easyCast: дополняем каждый AJAX-запрос служебными переменными Yii -->
<script>
$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    console.log(options);
    console.log(originalOptions);
});
</script>

<!-- IMPORTANT: APP CONFIG -->
<script src="<?= $themeUrl; ?>js/app.config.js"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events -->
<script src="<?= $themeUrl; ?>js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script>

<!-- BOOTSTRAP JS -->
<script src="<?= $themeUrl; ?>js/bootstrap/bootstrap.min.js"></script>

<!-- CUSTOM NOTIFICATION -->
<script src="<?= $themeUrl; ?>js/notification/SmartNotification.min.js"></script>

<!-- jGrowl: tiny notifications -->
<script src="<?= Yii::app()->baseUrl; ?>/js/jquery.jgrowl.min.js"></script>

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
<script src="<?= $themeUrl; ?>js/plugin/fastclick/fastclick.min.js"></script>

<!--[if IE 8]>
	<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
<![endif]-->

<!-- MAIN APP JS FILE -->
<script src="<?= $themeUrl; ?>js/app.min.js"></script>

<!-- ENHANCEMENT PLUGINS : NOT A REQUIREMENT -->
<!-- Voice command : plugin -->
<script src="<?= $themeUrl; ?>js/speech/voicecommand.min.js"></script>

<!-- SmartChat UI : plugin -->
<script src="<?= $themeUrl; ?>js/smart-chat-ui/smart.chat.ui.min.js"></script>
<script src="<?= $themeUrl; ?>js/smart-chat-ui/smart.chat.manager.min.js"></script>

<!-- Your GOOGLE ANALYTICS CODE Below -->
<!--script type="text/javascript"></script-->