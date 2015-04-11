<?php
/**
 * Верстка одного виджета JARVIS
 * 
 * @todo настройки
 * <!-- widget options:
		usage: <div class="jarviswidget" id="wid-id-0" 
		
		data-widget-editbutton="false"
		data-widget-colorbutton="false"	
		data-widget-editbutton="false"
		data-widget-togglebutton="false"
		data-widget-deletebutton="false"
		data-widget-fullscreenbutton="false"
		data-widget-custombutton="false"
		data-widget-collapsed="true" 
		data-widget-sortable="false"
	-->
 */
/* @var $this JarvisWidget */

?>
<!-- begin JARVIS widget-->
<?= CHtml::openTag('div', $this->htmlOptions); ?>
	<header>
        <span class="jarviswidget-loader">
            <i class="fa fa-refresh fa-spin"></i>
        </span>
		<h2><?= $this->title; ?></h2>				
	</header>
	<div><!-- widget div-->
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
	</div><!-- end widget div -->
<?= CHtml::closeTag('div'); ?>
<!-- end JARVIS widget -->
