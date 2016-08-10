var miniCartContent = '#mini-cart .dropdown-menu';

jQuery(function(){
    jQuery('.product-list-featured .tab a').click(function(){
        var id = jQuery(this).attr('rel');

        if(jQuery(this).hasClass('active'))
            return;

        jQuery('.product-list-featured .tab a').removeClass('active');
        jQuery(this).addClass('active');
        jQuery('.product-list-featured div.tab-content').addClass('no-display');
        jQuery('.product-list-featured div' + id).removeClass('no-display');
    });

    jQuery(document).on('click', '#mobile-menu', function(){
        if (!jQuery(this).attr('data-toggled') || jQuery(this).attr('data-toggled') == 'off'){
            jQuery(this).attr('data-toggled','on');
            jQuery('.vertnav-top').addClass('show');
            jQuery('#mobnav-trigger').addClass('active');
        }
        else if (jQuery(this).attr('data-toggled') == 'on'){
            jQuery(this).attr('data-toggled','off');
            jQuery('.vertnav-top').removeClass('show');
            jQuery('#mobnav-trigger').removeClass('active');
        }
    });

    jQuery(window).resize(function(){
        updateTemplate();
    });

    jQuery(window).ready(function(){
        updateTemplate();
    })

    jQuery(document).on('click', "#footer-links h4", function(){
        var windowWidth = jQuery(window).width();
        if(windowWidth >= 768){
            return;
        }

        var link = jQuery(this).parent();
        if(link.hasClass('active')){
            link.toggleClass('active').find('ul').stop().slideToggle('fast').focus();
            return;
        }

        jQuery('#footer-links div.nav-col').each(function(i, v){
            if(link != jQuery(v) && jQuery(v).hasClass('active')){
                 jQuery(v).toggleClass('active').find('ul').stop().slideToggle('fast');
            }
        });

        link.toggleClass('active').find('ul').stop().slideToggle('fast');
    });

    jQuery(document).on('touchstart', function (e) {
        clickOutSide(e.target);
    });

    jQuery(document).on('click', function( e ) {
        isIpad = navigator.userAgent.match(/iPad/i) != null;
        if(!isIpad){
            clickOutSide(e.target);
        }
    });

    jQuery(document).on('click', '#mobile-account-top', function(){
        jQuery('#mobile-account-top-links').stop().slideToggle('fast');
    });

    jQuery(document).on('click', '#popup-subcription-texts-subtitle-695cd596-ef28-4c9f-8715-67ad3dbe5059 a.claim', function(e) {
        jQuery("#popup-subcription-closes-link-695cd596-ef28-4c9f-8715-67ad3dbe5059").trigger("click");
    });
});

function clickOutSide(element){
    if(!jQuery(element).parents('#nav').length && jQuery('#nav .level0-wrapper').is(':visible')){
        jQuery('#nav .level0-wrapper').hide();
    }

    if(jQuery(element).parents(miniCartContent).length || jQuery(element).hasClass('dropdown-menu')){
        return false;
    }
    else{
        if(jQuery(miniCartContent).is(':visible')){
            jQuery(miniCartContent).hide();
            jQuery('#mini-cart').removeClass('open');
        }
        else{
            if(jQuery(element).parents('.feature-icon-hover').length && jQuery(element).parents('#mini-cart').length){
                jQuery(miniCartContent).show();
                jQuery('#mini-cart').addClass('open');
            }
        }

        return false;
    }
}

function updateTemplate(){
    var windowWidth = jQuery(window).width();

    if(windowWidth >= 768){
        jQuery('.product-list-featured #tab-1').removeClass('mobile');

        jQuery('#mobile-account-top-links').addClass('mobile');

        jQuery('#footer-links ul').show();

        if(jQuery('.slideshow-banners #home-rhs-why').length == 0)
            jQuery('#home-rhs-why').detach().insertAfter('.slideshow-banners .accessory-search');

        if(jQuery('.header-primary-container').next() != jQuery('.header-top-container'))
            jQuery(".nav-container").detach().insertAfter(".header-primary-container");


    }
    else if(windowWidth < 768){
        jQuery('#mobile-account-top-links').removeClass('mobile');

        jQuery('.product-list-featured #tab-1').addClass('mobile');

        if(jQuery('.slideshow-banners #home-rhs-why') != 0)
            jQuery('#home-rhs-why').detach().insertAfter('.product-list-featured');

        jQuery('#footer-links div').each(function(i, v){
            if(!jQuery(v).hasClass('active')){
                jQuery(v).find('ul').hide();
            }
        });

        if(jQuery('.header-top-container').next() != jQuery('.header-top-container'))
            jQuery(".nav-container").detach().insertAfter(".header-top-container");
    }
}

if (navigator.appName == "Microsoft Internet Explorer") {
    ie = true;
    //Create a user agent var
    var ua = navigator.userAgent;
    //Write a new regEx to find the version number
    var re = new RegExp("MSIE ([0-9]{1,}[.0-9]{0,})");
    //If the regEx through the userAgent is not null
    if (re.exec(ua) != null) {
        //Set the IE version
        ieVersion = parseInt(RegExp.$1);
        if(ieVersion == 9){
            jQuery('html').addClass('ie ie9 lt-ie10');
        }
        if(ieVersion == 10){
            jQuery('html').addClass('ie ie10 lt-ie10');
        }
    }
}