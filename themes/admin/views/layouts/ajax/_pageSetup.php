<?php
/**
 * Скрипт инициализации содержимого страницы
 * Обязательно нужен в конце каждой страницы темы smartAdmin для корректной работы
 * отвечает за подгрузку всего содержимого страницы через AJAX, а также за подключение 
 * js-файлов для виджетов, находящихся на странице
 * 
 * Функцию pageSetUp() править и перемещать нельзя, она служебная
 * Функцию pagefunction() править можно, но рекомендуется добавлять в нее данные при помощи
 * компонента EcClientScript который делает это автоматически 
 */
CVarDumper::dump(Yii::app()->clientScript->scriptFiles, 10, true);
CVarDumper::dump(Yii::app()->clientScript->scripts, 10, true);
?>
<script type="text/javascript">
    // [BEGIN_FINAL_CONTENT_INIT]
	/**
     * DO NOT REMOVE : GLOBAL FUNCTIONS!
	 *
	 * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
	 *
	 * // activate tooltips
	 * $("[rel=tooltip]").tooltip();
	 *
	 * // activate popovers
	 * $("[rel=popover]").popover();
	 *
	 * // activate popovers with hover states
	 * $("[rel=popover-hover]").popover({ trigger: "hover" });
	 *
	 * // activate inline charts
	 * runAllCharts();
	 *
	 * // setup widgets
	 * setup_widgets_desktop();
	 *
	 * // run form elements
	 * runAllForms();
	 *
	 ********************************
	 *
	 * pageSetUp() is needed whenever you load a page.
	 * It initializes and checks for all basic elements of the page
	 * and makes rendering easier.
	 *
	 */
	
	pageSetUp();
    
    // EcClientScript::POS_SETUP
    <?php 
    Yii::app()->clientScript->renderPageSetUp();
    ?>
    
	/**
	 * ALL PAGE RELATED SCRIPTS CAN GO BELOW HERE
	 * eg alert("my home function");
	 * 
	 * var pagefunction = function() {
	 *   ...
	 * }
	 * loadScript("js/plugin/_PLUGIN_NAME_.js", pagefunction);
	 * 
	 * TO LOAD A SCRIPT:
	 * var pagefunction = function (){ 
	 *  loadScript(".../plugin.js", run_after_loaded);	
	 * }
	 * 
	 * OR you can load chain scripts by doing
	 * 
	 * loadScript(".../plugin.js", function(){
	 * 	 loadScript("../plugin.js", function(){
	 * 	   ...
	 *   })
	 * });
	 */
	
	// pagefunction
	var pagefunction = function() {
		// EcClientScript::POS_PAGE
        <?php 
	    Yii::app()->clientScript->renderPageFunction();
	    ?>
	};
	// end pagefunction
	
	// destroy generated instances
	// pagedestroy is called automatically before loading a new page
	// (only usable for AJAX pages)
	var pagedestroy = function() {
	    // EcClientScript::POS_DESTROY
	    <?php 
	    Yii::app()->clientScript->renderPageDestroy();
	    ?>
	}
	// end destroy
	
	// run pagefunction
	pagefunction();
	// [END_FINAL_CONTENT_INIT]
</script>