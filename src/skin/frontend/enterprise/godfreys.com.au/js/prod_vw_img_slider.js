$j1_6(function(){
	var $ = $j1_6;	
	
	$('#product_thumbs_img').jcarousel({
		scroll:1,
		visible:4
	});
	
	var large_image;
	$('#product_thumbs_img li a').each(function(index) {
		$(this).bind("click", function( event ) {
			large_image = $(this).attr('href');
			$('#product_large_picture img').attr('src', large_image);
			return false; 
		});
	});
			
	
});
