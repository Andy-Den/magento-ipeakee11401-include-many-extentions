jQuery(document).ready(function () {
    jQuery("area[rel^='prettyPhoto']").prettyPhoto();

    jQuery(".media_info:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal', theme:'light_square', slideshow:3000, autoplay_slideshow:false});
    jQuery(".media_info:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast', slideshow:10000, hideflash:true});

});
