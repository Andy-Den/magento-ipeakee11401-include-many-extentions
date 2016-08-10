//jQuery.noconflict();
jQuery(document).ready(function() {
    var starCount;
    var isHold = '0%';
    jQuery('#star_controller').mousemove(function(e) {
        var offset = jQuery(this).offset();
        var relativeX = (e.pageX - offset.left);
        if (relativeX <= 19) {
            starCount = '20%';
        }
        if (relativeX <= 40 && relativeX > 19) {
            starCount = '40%';
        }
        if (relativeX <= 62 && relativeX > 40) {
            starCount = '60%';
        }
        if (relativeX <= 84 && relativeX > 62) {
            starCount = '80%';
        }
        if (relativeX <= 105 && relativeX > 84) {
            starCount = '100%';
        }

        jQuery(this).mouseout(function() {
            jQuery('#star_controller span').css({'width': isHold});
        });

        jQuery('#star_controller span').css({'width': starCount});
    });

    jQuery('#star_controller span').click(function() {
        jQuery('#star_controller span').css({'width': starCount});
        isHold = starCount;
        switch (isHold) {
            case "20%":
                jQuery('#product-review-table .val_1 ').attr('checked', 'checked');
                break;
            case "40%":
                jQuery('#product-review-table .val_2 ').attr('checked', 'checked');
                break;
            case "60%":
                jQuery('#product-review-table .val_3 ').attr('checked', 'checked');
                break;
            case "80%":
                jQuery('#product-review-table .val_4 ').attr('checked', 'checked');
                break;
            case "100%":
                jQuery('#product-review-table .val_5 ').attr('checked', 'checked');
                break;
            default: ;
                break;
        }
    });
});

