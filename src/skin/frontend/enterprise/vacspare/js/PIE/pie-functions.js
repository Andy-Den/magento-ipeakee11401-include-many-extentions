document.observe('dom:loaded', function(){
	if (window.PIE){
		$$('.css3').each(function(element){
			PIE.attach(element);
		});
	}
});
