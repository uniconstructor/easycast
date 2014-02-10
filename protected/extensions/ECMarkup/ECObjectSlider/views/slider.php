<?php
/**
 * 
 */
/* @var $this ECObjectSlider */
?>
<div class="list_carousel">
	<ul id="<?= $this->containerId; ?>">
	   <?php 
	   foreach ( $this->objects as $object )
	   {
	       echo "<li>\n";
	       echo $object;
	       echo "</li>\n";
	   }
	   ?>
	</ul>
	<div class="clearfix"></div>
	<a id="<?= $this->prevId; ?>" class="prev_news" href="#"></a>
	<a id="<?= $this->nextId; ?>" class="next_news" href="#"></a> 
</div>