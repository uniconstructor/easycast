/**
 *
 * Copyright (c) 2009 Tony Dewan (http://www.tonydewan.com/)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.tonydewan.com/code/checkToggle/
 * 
 */

(function($) {
	/**
	 * Version 1.0
	 * Replaces checkboxes with a toggle switch.
	 * usage: $("input[type='checkbox']").checkToggle(settings);
	 *
	 * @name  checkToggle
	 * @type  jquery
	 * @param Hash    settings					Settings
 	 * @param String  settings[on_label]		Text used for the left-side (on) label. Defaults to "On"
	 * @param String  settings[off_label]		Text used for the right-side (off) label. Defaults to "Off"
	 * @param String  settings[on_bg_color]		Hex background color for On state
	 * @param String  settings[off_bg_color]	Hex background color for Off state
	 * @param String  settings[skin_dir]		Document relative (or absolute) path to the skin directory
	 * @param Bool    settings[bypass_skin]		Flags whether to bypass the inclusion of the skin.css file.  Used if you've included the skin styles somewhere else already.
	 */
    $.fn.echeckToggle = function(settings) {
   
		settings = $.extend({
			on_label	: 'On',
			on_bg_color	: '#8FE38D', 
			off_label	: 'Off',
			off_bg_color: '#F8837C',
			skin_dir	: "skin/",
			bypass_skin : false,
            after_on    : function () {},
            after_off   : function () {}
		}, settings);
        
		// append the skin styles
		if(settings.bypass_skin == false){
			$("head").append('<link type="text/css" rel="stylesheet" href="'+settings.skin_dir+'skin.css" media="screen" />');
		}
        
        /*var etogglebox_toggle_on = function ()
        {
            
        }
        var etogglebox_toggle_off = function ()
        {

        }*/
		
		var etogglebox_toggle = function(element){
			
			var checked = $(element).parent().parent().prev().attr("checked");
			
			// if it's set to on
			if(checked){
				
				$(element).animate({marginLeft: '2.125em'}, 100, 
				
				// callback function
				function(){
					$(element).parent().prev().css("color","#fff");
                    $(element).parent().prev().css("font-weight","normal");
					$(element).parent().next().css("color","#222");
                    $(element).parent().next().css("font-weight","bold");
                    
					$(element).parent().css("background-color", settings.off_bg_color);
					$(element).parent().parent().prev().removeAttr("checked");
					$(element).removeClass("left").addClass("right");
                    settings.after_off();
				});

			}else{
			
				$(element).animate({marginLeft: '0em'}, 100, 
				
				// callback function
				function(){
					$(element).parent().prev().css("color","#222");
                    $(element).parent().prev().css("font-weight","bold");
					$(element).parent().next().css("color","#fff");
                    $(element).parent().next().css("font-weight","normal");
                    
					$(element).parent().css("background-color", settings.on_bg_color);
					$(element).parent().parent().prev().attr("checked","checked");
					$(element).removeClass("right").addClass("left");
                    settings.after_on();
				});

			}
		
		};
        
        // hide the checkbox
        $('#' + this.selector).css('display','none');
        $('#' + this.selector).hide();
        
        // insert the new toggle markup
        if( $('#' + this.selector).attr("checked") == true || $('#' + this.selector).attr("checked") == 'checked' )
        {
            $('#' + this.selector).after('<div class="toggleSwitch"><span class="leftLabel" style="font-weight:bold;color:#222;">'+settings.on_label+'<\/span><div class="switchArea" style="background-color: '+settings.on_bg_color+'"><span class="switchHandle left" style="margin-left: 0em;"><\/span><\/div><span class="rightLabel" style="color:#fff;">'+settings.off_label+'<\/span><\/div>');
        }else
        {
            $('#' + this.selector).after('<div class="toggleSwitch"><span class="leftLabel" style="color:#fff;">'+settings.on_label+'<\/span><div class="switchArea" style="background-color: '+settings.off_bg_color+'"><span class="switchHandle right" style="margin-left:2.125em"><\/span><\/div><span class="rightLabel" style="font-weight:bold;color:#222;">'+settings.off_label+'<\/span><\/div>');
        }			
        //console.log($('#' + this.selector).attr("checked"));
        //console.log(this.attr("checked"));
        // Bind the switchHandle click events to the internal toggle function			
        $('#' + this.selector).next().find('span.switchHandle').bind("mousedown", function () { etogglebox_toggle(this); })
            
        return this;
	};

})(jQuery);