<?php 
/**
 * Пример максимально полной разметки страницы
 * 
 * @todo заготовка для верстки
 */
?>
<!-- Bread crumb is created dynamically -->
<!-- row -->
<div class="row">
	<!-- col -->
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
		<h1 class="page-title txt-color-blueDark">
            <!-- PAGE HEADER -->
            <i class="fa fa-lg fa-fw fa-cube"></i>SmartAdmin Intel
            <span>App Settings</span>
		</h1>
	</div>
	<!-- end col -->
</div>
<!-- end row -->
<!--
    The ID "widget-grid" will start to initialize all widgets below
    You do not need to use widgets if you dont want to. Simply remove
    the <section></section> and you can use wells or panels instead
-->
<!-- widget grid -->
<section id="widget-grid" class="">
	<!-- row -->
	<div class="row">
		<!-- NEW WIDGET START -->
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-7">
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget" id="wid-id-0" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
				<header>
					<h2> Menu Toggle / Collapse / Minify </h2>
					<span class="badge pull-right margin-right-5 margin-top-5">new</span>
				</header>
				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">
					</div>
					<!-- end widget edit box -->
					<!-- widget content -->
					<div class="widget-body no-padding">
						<!-- this is what the user will see -->
						<div class="padding-gutter">
							To toggle minify menu manually add the class <code>
								minified</code>
							to the <b><i>BODY</i></b> element. To collapse the main menu on desktops, add class <code>
								hidden-menu</code>
							to the <b><i>BODY</i></b> element.
							<br>
							<br>
							<span class="btn btn-default" data-action="minifyMenu"> Toggle .minify class </span>
							&nbsp;&nbsp;
							<button class="btn btn-default" data-action="toggleMenu">
								Toggle .collapse class
							</button>
						</div>
						<div class="table-responsive no-margin">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th><b><i>attribute*</i></b></th>
										<th>Description</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
										<code>
											data-action="userLogout"
										</code></td>
										<td>Logout message popup, use it with <code>
											data-logout-msg = "..."</code></td>
										<td><a href="login.html" class="btn btn-default btn-xs" data-action="userLogout" data-logout-msg="Your message here..."> action </a></td>
									</tr>
									<tr>
										<td>
										<code>
											data-action="resetWidgets"
										</code></td>
										<td>Resets all localStorage <i>(restores all app settings and widgets)</i></td>
										<td>
										<button class="btn btn-default btn-xs" data-action="resetWidgets">
											action
										</button></td>
									</tr>
									<tr>
										<td>
										<code>
											data-action="launchFullscreen"
										</code></td>
										<td>Launch full screen view <i>(works only in Chrome, Safari, Firefox and Latest IE)</i></td>
										<td>
										<button class="btn btn-default btn-xs" data-action="launchFullscreen">
											action
										</button></td>
									</tr>
									<tr>
										<td>
										<code>
											data-action="minifyMenu"
										</code></td>
										<td>Minify main nav <i>(works only with vertical menu case)</i></td>
										<td>
										<button class="btn btn-default btn-xs" data-action="minifyMenu">
											action
										</button></td>
									</tr>
									<tr>
										<td>
										<code>
											data-action="toggleMenu"
										</code></td>
										<td>Collapse left menu <i>(but still accessable by hovering left edge of screen)</i></td>
										<td>
										<button class="btn btn-default btn-xs" data-action="toggleMenu">
											action
										</button></td>
									</tr>
									<tr>
										<td>
										<code>
											data-action="toggleShortcut"
										</code></td>
										<td>Top slidedown / Metro menu toggle</td>
										<td>
										<button class="btn btn-default btn-xs" data-action="toggleShortcut">
											action
										</button></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<!-- end widget content -->
				</div>
				<!-- end widget div -->
			</div>
			<!-- end widget -->
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget" id="wid-id-1" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
				<header>
					<h2>App Settings</h2>
				</header>
				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">
					</div>
					<!-- end widget edit box -->
					<!-- widget content -->
					<div class="widget-body no-padding">
						<!-- this is what the user will see -->
						<div class="padding-gutter">
							<span class="badge bg-color-green">Note:</span> You can adjust these settings inside <span class="text-primary">app.js</span> file to your comfort.
						</div>
						<div class="table-responsive">
							<table class="table table-bordered margin-top-10">
								<thead>
									<tr>
										<th>Name</th>
										<th>Default/Value</th>
										<th>Description</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><b><i>$.throttle_delay</i></b></td>
										<td>
										<code>
											350
										</code></td>
										<td>Impacts the responce rate of some of the responsive elements (lower value affects CPU but improves speed)</td>
									</tr>
									<tr>
										<td><b><i>$.menu_speed</i></b></td>
										<td>
										<code>
											235
										</code></td>
										<td>The rate at which the menu expands revealing child elements on click</td>
									</tr>
									<tr>
										<td><b><i>$.navAsAjax</i></b></td>
										<td>
										<code>
											true/false
										</code></td>
										<td>Your left nav in your app will no longer fire ajax calls, set it to false for HTML version</td>
									</tr>
									<tr>
										<td><b><i>$.enableJarvisWidgets</i></b></td>
										<td>
										<code>
											true/false
										</code></td>
										<td>Please make sure you have included "jarvis.widget.min.js" in your page for this below feature to work</td>
									</tr>
									<tr>
										<td><b><i>$.enableMobileWidgets</i></b></td>
										<td>
										<code>
											true/false
										</code></td>
										<td>Warning: Enabling mobile widgets could potentially crash your webApp if you have too many widgets running at once (<i>must have <b><i>$.enableJarvisWidgets</i></b> to
										<code>true</code></i>)</td>
									</tr>
									<tr>
										<td><b><i>closedSign</i></b></td>
										<td>
										<code>
											fa-plus-square-o
										</code></td>
										<td>Menu open icon</td>
									</tr>
									<tr>
										<td><b><i>openedSign</i></b></td>
										<td>
										<code>
											fa-minus-square-o
										</code></td>
										<td>Menu close icon</td>
									</tr>
									<tr>
										<td><b><i>setup_widgets_desktop()</i></b></td>
										<td><i>function<b>()</b></i></td>
										<td>Setup widgets for desktop (<i>must have <b><i>$.enableJarvisWidgets</i></b> to
										<code>
											true
										</code></i>) </td>
									</tr>
									<tr>
										<td><b><i>setup_widgets_mobile()</i></b></td>
										<td><i>function<b>()</b></i></td>
										<td>Setup widgets for desktop <i>(must have <b>$.enableJarvisWidgets</b> and <b>$.enableMobileWidgets</b> to <code>true</code>)</i></td>
									</tr>
									<tr>
										<td><b><i>runAllCharts()</i></b></td>
										<td><i>function<b>()</b></i></td>
										<td>Runs all inline charts including: $.sparkline and $.easyPieChart</td>
									</tr>
									<tr>
										<td><b><i>runAllForms()</i></b></td>
										<td><i>function<b>()</b></i></td>
										<td>Runs all form related scripts such as $.select2, $.mask, $.datepicker and $.autocomplete</td>
									</tr>
									<tr>
										<td><b><i>pageSetUp()</i></b></td>
										<td><i>function<b>()</b></i></td>
										<td>Runs the following functions all at once: <i>setup_widgets_desktop<b>()</b>, setup_widgets_mobile<b>()</b>, runAllCharts<b>()</b>, runAllForms<b>()</b></i> - and also activates all tooltip and popovers</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<!-- end widget content -->
				</div>
				<!-- end widget div -->
			</div>
			<!-- end widget -->
		</article>
		<!-- WIDGET END -->
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-5">
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget" id="wid-id-2" data-widget-colorbutton="false"	data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false">
				<header>
					<h2> Page layout options </h2>
				</header>
				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
						<input class="form-control" type="text">
					</div>
					<!-- end widget edit box -->
					<!-- widget content -->
					<div class="widget-body no-padding">
						<div class="padding-gutter padding-bottom-0">
							Possible classes for the <b><i>BODY</i></b> tag: <code>
								smart-skin-{SKIN_NUMBER}</code>
							, <code>
								smart-rtl</code>
							, <code>
								fixed-header</code>
							, <code>
								fixed-navigation</code>
							, <code>
								fixed-ribbon</code>
							, <code>
								fixed-footer</code>
							, <code>
								container</code>
						</div>
						<!-- this is what the user will see -->
						<div class="padding-gutter">
							<div style="background: #f1f1f1;border-radius:3px;overflow: hidden">
								<div style="width: 100%;height: auto;background:#ddd;font-size:11px;font-weight: bold;padding:13px 10px">
									<img src="img/logo.png" alt="SmartAdmin" style="width:75px;">
								</div>
								<div style="width: 25%;height: 296px; background:#949291;" class="pull-left"></div>
								<div style="width: 75%;height: 255px; background:#f1f1f1;" class="pull-left">
									<div style="width: 100%; height:20px; padding:3px; background:#C5C5C5; font-size:10px; font-weight: bold;">
										<i class="fa fa-home"></i> breadcrumb &gt;
									</div>
								</div>
								<div style="width: 75%;height: auto;background:#ddd;font-size:11px;font-weight: bold;padding:13px 10px" class="text-right pull-right">
									Footer
								</div>
							</div>
						</div>
						<hr>
						<div class="padding-gutter padding-top-0 padding-bottom-0">
							Switch to top menu by adding class <code>
								.menu-on-top</code>
						</div>
						<div class="padding-gutter">
							<div style="background: #f1f1f1;border-radius:3px;overflow: hidden">
								<div style="width: 100%;height: auto;background:#ddd;font-size:11px;font-weight: bold;padding:13px 10px">
									<img src="img/logo.png" alt="SmartAdmin" style="width:75px;">
								</div>
								<div style="width: 100%;height: 50px; background:#949291;" class="pull-left"></div>
								<div style="width: 100%;height: 255px; background:#f1f1f1;" class="pull-left">
									<div style="width: 100%; height:20px; padding:3px; background:#C5C5C5; font-size:10px; font-weight: bold;">
										<i class="fa fa-home"></i> breadcrumb >
									</div>
								</div>
								<div style="width: 100%;height: auto;background:#ddd;font-size:11px;font-weight: bold;padding:13px 10px" class="text-right pull-right">
									Footer
								</div>
							</div>
						</div>
					</div>
					<!-- end widget content -->
				</div>
				<!-- end widget div -->
			</div>
			<!-- end widget -->
		</article>
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
	 * OR
	 * 
	 * loadScript(".../plugin.js", run_after_loaded);
	 */
	
	// pagefunction
	
	var pagefunction = function() {
		
	};
	
	// end pagefunction
	
	// run pagefunction on load

	pagefunction();

</script>
