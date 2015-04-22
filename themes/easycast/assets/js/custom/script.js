$().ready(function() 
{   

	$(".block").affix({
        offset: { 
            top: 300 
     	}
    });
    
    $('form').submit(function(){
    
        $('input[type=submit]').attr("disabled", "disabled");  
        $('button[type=submit]').attr("disabled", "disabled");  
        
        return true;
    });
    
    $('.checkbox input').iCheck({
        checkboxClass: 'icheckbox_minimal-orange',
        increaseArea: '20%'
    });

    $('.radio input').iCheck({
        radioClass: 'iradio_minimal-orange',
        increaseArea: '20%'
    });
    
	$('#quote-carousel').carousel({
		pause: true,
		interval: 1114000,
	});
	
	$('#sections-choose a').on('click', function(){
	    var sel = $(this).data('title');
	    var tog = $(this).data('toggle');
	    $('#'+tog).prop('value', sel);
	    
	    $('a[data-toggle="'+tog+'"]').not('[data-title="'+sel+'"]').removeClass('active').addClass('not-active');
	    $('a[data-toggle="'+tog+'"][data-title="'+sel+'"]').removeClass('not-active').addClass('active');
	});
    
    imagesLoaded("div.profile-user-photos", function(){
		$('div.profile-user-photos').masonry({ itemSelector: '.one', "isOriginLeft": true, "gutter": 20});
    });
    
    /*
    var items = $('.btn-nav');
    $( ".btn-nav" ).click(function() {
        $( items[activeEl] ).removeClass('active');
        $( this ).addClass('active');
        activeEl = $( ".btn-nav" ).index( this );
    });
	
	*/  
	
});