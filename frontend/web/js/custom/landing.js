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
}); // close out script

