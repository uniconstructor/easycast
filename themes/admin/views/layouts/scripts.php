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
 * @todo подключить JS TOUCH для мобильных устройств
 */
/* @var $this Controller */

// путь к корню темы оформления (там лежат все скрипты и стили)
$themeUrl = Yii::app()->theme->baseUrl.'/assets/';

// временно отключенные библиотеки
// <!-- FastClick: For mobile devices: you can disable this in app.js -->
// <!-- script src="< ?= $themeUrl; ? >js/plugin/fastclick/fastclick.js"></script -->
// <!-- JS TOUCH : include this plugin for mobile drag / drop touch events -->
// <!-- script src="< ?= $themeUrl; ? >js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script-->
?>
<!-- END SHORTCUT AREA -->
<!--================================================== -->
<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices) -->
<script data-pace-options='{ "restartOnRequestAfter": true }' src="<?= $themeUrl; ?>js/plugin/pace/pace.min.js"></script>
<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="<?= $themeUrl; ?>js/libs/jquery-2.1.1.min.js"></script>
<script src="<?= $themeUrl; ?>js/libs/jquery-ui-1.10.3.min.js"></script>
<!-- IMPORTANT: APP CONFIG -->
<script src="<?= $themeUrl; ?>js/app.config.js"></script>
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
<!-- MAIN APP JS FILE -->
<script src="<?= $themeUrl; ?>js/app.min.js"></script>
<!-- jGrowl: tiny notifications -->
<script src="<?= Yii::app()->baseUrl; ?>/js/jquery.jgrowl.min.js"></script>