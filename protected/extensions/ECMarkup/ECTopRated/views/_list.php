<?php
/**
 * Список актеров в ассорти на главной
 */
/* @var $this ECTopRated */
?>
<div class="tab-pane fade active in" id="current">
    <div class="ec_slider_container2">
    	<div class="slider_fix"></div>
    	<div class="list_carousel">
    		<ul id="slider_photo" class="slider_photogallery">
    			<?php 
    			foreach ( $this->dataProvider->getData() as $item )
    			{
    			    $image = CHtml::image($item['preview'], $item['name']);
    			    $link  = CHtml::link($image, $item['link'], array('target' => '_blank'));
    			    echo '<li>'.$link.'</li>';
    			}
    			?>
    		</ul>
    		<div class="clearfix"></div>
    		<a id="prev" class="prev" href="#"></a>
    		<a id="next" class="next" href="#"></a>
    		<?= $this->viewAll; ?>
    	</div>
    </div>
</div>