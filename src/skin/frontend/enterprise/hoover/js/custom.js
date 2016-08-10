jQuery(document).ready(function () {

    jQuery('.frame').hover(function () {
        jQuery('.s_item', this).stop().animate({'left':'-110px'}, '500');
    }, function () {
        jQuery('.s_item', this).stop().animate({'left':'0px'});
    });
});

jQuery(document).ready(function () {
    var clicked = false;
    var inpText = jQuery('#search').val();
    if (inpText == '') {
        jQuery('#search').val('Search products here...');
    }


    jQuery('#search').click(function () {
        if (jQuery('#search').val() == 'Search products here...') {
            jQuery('#search').val('');
            clicked = true;
        }
    });

    jQuery('#search').focusout(function ()
    {
        if (jQuery('#search').val() == '' && clicked == true) {
            jQuery('#search').val('Search products here...');
            clicked = false;
        }
    });

    jQuery('#search_mini_form').submit(function (e) {
        if (jQuery('#search').val() == 'Search products here...' || jQuery('#search').val() == '') {
            e.preventDefault();
            return false;
        }
    });

    var currentWidth = jQuery(window).width()-20;
    if(currentWidth <768 && (jQuery('.locator-location-index').length || jQuery('.locator-search-index').length) ) {
        var locatorWidth = currentWidth - 20;
        jQuery('.col-main').css('width',currentWidth+'px');
        jQuery('.locator-search, .locator-state').css('width',locatorWidth+'px');
        var widthOnView = locatorWidth-10;
        jQuery('.loc-page').css('width',widthOnView+'px');
        jQuery('.loc-srch-res-list').css('width',locatorWidth+'px');
    }


    var resizeTimer;
    jQuery(window).on('resize', function(e) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            var currentWidth = jQuery(window).width()-20;
            if(currentWidth <768 && (jQuery('.locator-location-index').length || jQuery('.locator-search-index').length) ) {
                jQuery('.col-main').css('width',currentWidth+"px");
                var locatorWidth = currentWidth-20;
                jQuery('.locator-search, .locator-state').css('width',locatorWidth+'px');
                var widthOnView = locatorWidth-10;
                jQuery('.loc-page').css('width',widthOnView+'px');
                jQuery('.loc-srch-res-list').css('width',locatorWidth+'px');
            }
            else {
                jQuery('.col-main').css('width',"auto");
                jQuery('.locator-search, .locator-state').css('width','100%');
                jQuery('.loc-page').css('width','100%');
                jQuery('.loc-srch-res-list').css('width','auto');
            }
        }, 250);

    });




});

function showtab(id) {
    names = new Array("tabname_1", "tabname_2"); //массив id заголовков табов
    conts = new Array("tabcontent_1", "tabcontent_2"); //массив id табов
    for (i = 0; i < names.length; i++) {
        document.getElementById(names[i]).className = 'nonactive';
    }
    for (i = 0; i < conts.length; i++) {
        document.getElementById(conts[i]).className = 'hide';
    }

    document.getElementById('tabname_' + id).className = 'active';
    document.getElementById('tabcontent_' + id).className = 'show';
}
