/* Godfreys Amasty Navigation Menu Stuff */

jQuery(document).ready(function() {
    jQuery('dl#narrow-by-list dt').click(function() {
        jQuery(this).next().slideToggle('fast');
        jQuery(this).toggleClass('active');
        return false;
    }).next().hide();
    
	jQuery('dl#narrow-by-list dt').first().toggleClass('active');
    jQuery('dl#narrow-by-list dd').first().show();
	
	jQuery('dl#narrow-by-list dt.price').toggleClass('active');
	jQuery('dl#narrow-by-list dd.price').show();

    jQuery('dl#narrow-by-list dd').each(function(index) {

        if (jQuery(this).find('a.amshopby-attr-selected').length > 0) {
            jQuery(this).prev('dt').toggleClass('active');
            jQuery(this).show();
        }
    })

})