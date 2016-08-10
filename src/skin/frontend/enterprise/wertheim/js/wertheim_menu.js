
jQuery(document).ready(function() {
    

    
    
    // menu
            

    jQuery("ul#topnav li").hover(function() {
        jQuery(this).find("a").css({
            'color' : '#83d84c',
            'border-bottom' : '1px solid #72b746'
        });
         
             
        //jQuery(this).find("ul").show();
        
       
    } , function() { 
        jQuery(this).find("a").css({
            'color' : '#F0F0F0',
            'border-bottom' : 'none'
        });
        
        //jQuery(this).find("ul").hide();
    });
});

