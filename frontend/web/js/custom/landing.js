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

var swiper = new Swiper('.services-swiper-container', {
    effect: 'coverflow',
    initialSlide : 3,
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 'auto',
    coverflow: {
        rotate: 30,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows : false
    }
});

var swiper = new Swiper('.projects-swiper-container', {
    effect: 'coverflow',
    initialSlide : 3,
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 'auto',
    coverflow: {
        rotate: 30,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows : false
    }
});

var swiper = new Swiper('.types-swiper-container', {
    effect: 'coverflow',
    initialSlide : 2,
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 'auto',
    coverflow: {
        rotate: 30,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows : false
    }
});

var swiper = new Swiper('.users-swiper-container', {
    effect: 'coverflow',
    initialSlide : 1,
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: 'auto',
    slideToClickedSlide: true,
    coverflow: {
        rotate: 30,
        stretch: 0,
        depth: 100,
        modifier: 1,
        slideShadows : false
    }
});

        var swiper = new Swiper('.reviews-swiper-container', {
            pagination: '.swiper-pagination',
            initialSlide : 1,
            effect: 'coverflow',
            grabCursor: true,
            centeredSlides: true,
            slideToClickedSlide: true,
            slidesPerView: 'auto',
            coverflow: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows : true
            }
        });

        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            initialSlide : 1,
            effect: 'coverflow',
            grabCursor: true,
            centeredSlides: true,
            slideToClickedSlide: true,
            slidesPerView: 'auto',
            coverflow: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows : true
            }
        });
/*
function sdf_FTS(_number,_decimal,_separator)
{
        var decimal=(typeof(_decimal)!='undefined')?_decimal:2;
        var separator=(typeof(_separator)!='undefined')?_separator:'';
        var r=parseFloat(_number)
        var exp10=Math.pow(10,decimal);
        r=Math.round(r*exp10)/exp10;
        rr=Number(r).toFixed(decimal).toString().split('.');
        b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
        r=(rr[1]?b+'.'+rr[1]:b);

        return r;
}
setTimeout(function(){
        $('#counter').text('0');
        $('#counter1').text('0');
        $('#counter2').text('0');
        setInterval(function(){

                var curval=parseInt($('#counter').text());
                var curval1=parseInt($('#counter1').text().replace(' ',''));
                var curval2=parseInt($('#counter2').text());
                if(curval<=707){
                        $('#counter').text(curval+1);
                }
                if(curval1<=12280){
                        $('#counter1').text(sdf_FTS((curval1+20),0,' '));
                }
                if(curval2<=245){
                        $('#counter2').text(curval2+1);
                }
        }, 2);

}, 500);
*/