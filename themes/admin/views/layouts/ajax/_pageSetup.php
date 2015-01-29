<?php
/**
 * Скрипт инициализации содержимого страницы
 * Обязательно нужен в конце каждой страницы темы smartAdmin для корректной работы
 * отвечает за подгрузку всего содержимого страницы через AJAX, а также за подключение 
 * js-файлов для виджетов, находящихся на странице
 * 
 * Функцию pageSetUp() править и перемещать нельзя, она служебная
 * Функцию pagefunction() править можно, но рекомендуется добавлять в нее данные при помощи
 * компонента SmartClientScript который делает это автоматически 
 * 
 * @todo модернизировать CClientScript таким образом, чтобы все подключаемые скрипты
 *      (как файлы так и отдельные строки) автоматически добавлялись внутрь pagefunction(){...}
 *      Переписать renderBodyEnd()
 *      Добавить renderPageSetUp() [синоним renderBodyEnd()] и renderPageDestroy()
 */
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
    
    // Скрипты регистрируемые компонентом SmartClientScript
    // %SCC_POS_SETUP% //
    
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
		// Скрипты регистрируемые компонентом SmartClientScript
        // %SCC_POS_PAGE% //
	};
	// end pagefunction
	
	// pagedestroy is called automatically before loading a new page
	// (only usable for AJAX pages)
	var pagedestroy = function(){
	    // destroy generated instances 
	    // Скрипты регистрируемые компонентом SmartClientScript
        // %SCC_POS_PAGE% //
	}
	// end destroy
	
	// run pagefunction
	pagefunction();
	// [END_FINAL_CONTENT_INIT]
</script>