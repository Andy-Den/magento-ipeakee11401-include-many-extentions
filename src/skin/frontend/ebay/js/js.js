(function($){
    $(document).ready(function($){
        if (videolink != '') {
            jQuery(".video").html('<iframe src="' + videolink + '" frameborder="0" allowfullscreen="" width="100%" height="260"></iframe>');
        }
        $('.jcarousel').jCarouselLite({
            btnPrev: ".jcarousel-control-prev",
            btnNext: ".jcarousel-control-next",
            visible: 8
        });

        $('.jcarousel li').each(function(){
            $(this).mouseenter(function(){
                var tmp = $(".tooltip",this);
                console.log(tmp.html());
                var offset = $(this).offset();
                if (tmp != undefined ){
                    $( tmp ).clone(true).appendTo( "#box-tooltip" );
                    $("#box-tooltip").css({"left":(offset.left+130), "top": (offset.top+5), "display":"block"});
                }

            });
            $(this).mouseleave(function(){
                $("#box-tooltip").html("");
            });

        });
    });
})(jQuery);