<?php
/**
 * Верстка сетки из нескольких виджетов
 */
/* @var $this SmartWidgetGrid */
?>
<!--
	The ID "widget-grid" will start to initialize all widgets below 
	You do not need to use widgets if you dont want to. Simply remove 
	the <section></section> and you can use wells or panels instead 
	-->
<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">
        <!-- NEW WIDGET SECTION (ROW/COULUMN) -->
		<?php 
		foreach ( $this->sections as $id => $section )
		{// перебираем все группы виджетов и выводим содержимое каждой
		    echo CHtml::openTag('article', $section['htmlOptions']);
		    foreach ( $section['widgets'] as $widget )
		    {
		        if ( is_array($widget) )
		        {// виджет задан массивом настроек
		            $this->widget('ext.smart.SmartWidget', $widget);
		        }else
		        {// виджет уже является строкой
		            echo $widget;
		        }
		    }
		    echo CHtml::closeTag('article');
		}
		?>
		<!-- SUBGRID END -->
	</div>
	<!-- end row -->
	<!-- row -->
	<div class="row">
		<!-- a blank row to get started -->
		<div class="col-sm-12">
			<!-- your contents here -->
		</div>
	</div>
	<!-- end row -->
</section>
<!-- end widget grid -->

<script type="text/javascript">
	
	/* DO NOT REMOVE : GLOBAL FUNCTIONS!
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
	
	/*
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
		// clears the variable if left blank
	};
	
	// end pagefunction
	
	// run pagefunction
	pagefunction();
	
</script>
