(function($){

	var btrTab = function(element) {
		
        if ( location.hash ) {
        	
        	window.scrollTo(0, 0);
        	
        	setTimeout( function() {
        	    window.scrollTo(0, 0);
        	}, 1);
        	
        	element.find('ul a[href$="' + location.hash + '"]').parent().addClass('active');
        
        } else {
        
        	element.find('ul li:first').addClass('active');
        	
        }
                        
        element.find('ul').next().find('div' + element.find('li.active a').attr('href')).show();
        
        $('a.btr-tabs-anchor').click( function(e) {
        	
        	e.preventDefault();
        	
        	element.find('ul a[href="' + $(this).attr('href') + '"]').trigger('click');
        
        });
        
        element.find('ul li a').click( function(e) {
        
        	e.preventDefault();
        	
        	var active_link = $(this);
        	
        	if ( !$(this).parent().hasClass('active') )	{
        	
	        	element.find('div' + element.find('ul li.active a').attr('href')).stop(true, true).fadeOut(100, function() {
	        		
	        		element.find('ul li').removeClass('active');
	        		active_link.parent().addClass('active');
	        		
	        		element.find('ul')
	        			.next()
	        			.find('div' + element.find('ul li.active a').attr('href'))
	        			.fadeIn(100);
	        	
	        	});
	        
	        }
        	
        });

    };
	
	$(document).ready(function($){
    	
    	$(".btr-tabs").each(function() {
            new btrTab($(this));
        });
    			  
	});
	
})(jQuery);