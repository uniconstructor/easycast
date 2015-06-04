$(".fancybox-thumb").fancybox({
        prevEffect	: 'none',
        nextEffect	: 'none',
        helpers	: {
                title	: {
                        type: 'outside'
                },
                thumbs	: {
                        width	: 100,
                        height	: 100
                }
        }
});

$(document).ready(function(){


        $("li.media").click(function(){
                var slideID = $(this).attr("id");
    slideID = this.id.substr(this.id.lastIndexOf("-") + 1);
    slideID = parseInt(slideID);

                $(".event-stripe.active").removeClass("active");
                $("li.media.active").removeClass("active");

                $(".event-stripe#slide-" + slideID).addClass("active");
                $(this).addClass("active");
        });

   // cache the window object
   $window = $(window);

   $('section[data-type="background"]').each(function(){
     // declare the variable to affect the defined data-type
     var $scroll = $(this);

      $(window).scroll(function() {
        // HTML5 proves useful for helping with creating JS functions!
        // also, negative value because we're scrolling upwards                             
        var yPos = -($window.scrollTop() / $scroll.data('speed')); 

        // background position
        var coords = '50% '+ yPos + 'px';

        // move the background
        $scroll.css({ backgroundPosition: coords });    
      }); // end window scroll
   });  // end section function
}); // close out script

