/**
 * jquery.slidetoggle.js
 *
 * Part of Yii extension 'slidetoggle'
 * @author Joe Blocher
 *
 */

jQuery.fn.eslidetoggle = function(options) {

	var defaults = {
		duration: 'slow',
		easing: 'swing',
		classCollapsed: 'slidetoggle-collapsed'
	}

	var settings = jQuery.extend({}, defaults, options);
    
	$(this).click(function(){
        jQuery(this).parent().children().not(this).fadeToggle(settings.duration,settings.easing);
	    $(this).toggleClass(settings.classCollapsed);
        
		return false; //No jump to the link anchor
	});

};