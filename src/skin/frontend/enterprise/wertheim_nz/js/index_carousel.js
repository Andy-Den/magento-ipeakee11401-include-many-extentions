jQuery(document).ready(function() {
    // carousel
    jQuery('#home_carousel').jcarousel({
        // wrap: 'circular', //both
        animation: 'slow',
        visible: 1,
        scroll: 1,
        start: 1
    });
    jQuery('#carousel_reviews').jcarousel({
        // wrap: 'circular', //both
        animation: 'slow',
        visible: 1,
        scroll: 1,
        start: 1
    });
});