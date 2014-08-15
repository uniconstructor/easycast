<?php
/**
 * Верстка одного виджета JARVIS
 * @todo настройки
 */
/* @var $this SmartWidget */


?>

<!-- Widget ID (each widget will need unique ID)-->
<?= CHtml::openTag('div', $this->htmlOptions); ?>
<div class="jarviswidget" id="wid-id-0">
	<!-- widget options:
		usage: <div class="jarviswidget" id="wid-id-0" data-widget-editbutton="false">
		
		data-widget-colorbutton="false"	
		data-widget-editbutton="false"
		data-widget-togglebutton="false"
		data-widget-deletebutton="false"
		data-widget-fullscreenbutton="false"
		data-widget-custombutton="false"
		data-widget-collapsed="true" 
		data-widget-sortable="false"
		
	-->
	<header>
		<h2><?= $this->title; ?></h2>				
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
		<div class="widget-body">
			<?= $this->content; ?>
		</div>
		<!-- end widget content -->
	</div>
	<!-- end widget div -->
<?= CHtml::closeTag('div'); ?>
<!-- end widget -->
