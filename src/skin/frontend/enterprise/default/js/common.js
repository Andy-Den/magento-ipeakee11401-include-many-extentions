$j1_6(function(){
var $ = $j1_6;	
$('.more-options').each(function(){
		var ml = $(this);
		ml.click(function(e){
			e.preventDefault();
			var ml = $(this);
			var mlp = ml.next('.more-links');
			mlp.toggle();
			ml.toggleClass('active');
		});
	});

$('#main-banner-img').cycle({
    timeout: 0,
	speed:   500, 
	timeout: 5000,
    pager:  '#slidenav'
});
});