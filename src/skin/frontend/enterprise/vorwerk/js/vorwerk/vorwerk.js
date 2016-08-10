jQuery(document).ready(function(){
    jQuery('.button-lookalike.green, .button-lookalike.orange').each(function(){
        if(jQuery(this).children('span').length>0){
            dataHtml = jQuery(this).children('span').html();
        }else{
            dataHtml = jQuery(this).html();
        }
        htmlContent = '<span class="buttonleft"></span><span class="buttonmid"><span class="buttoniconright arrow-right">'+ dataHtml +'</span></span><span class="buttonright"></span>';
        jQuery(this).html(htmlContent);
    })
    jQuery('.product-sidebar-content .button-lookalike').each(function(){
        jQuery(this).addClass('primary');
        jQuery(this).addClass('green');
        if(jQuery(this).children('span').length>0){
            dataHtml = jQuery(this).children('span').html();
        }else{
            dataHtml = jQuery(this).html();
        }
        htmlContent = '<span class="buttonleft"></span><span class="buttonmid"><span class="buttoniconright arrow-right">'+ dataHtml +'</span></span><span class="buttonright"></span>';
        jQuery(this).html(htmlContent);

    })
})