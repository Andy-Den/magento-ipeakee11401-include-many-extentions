$j1_6(function(){
	var $ = $j1_6;
	
	$('#brands-new').lemmonSlider({
		'infinite' : true
	});
	
	function sliderControlObject() {

	   this.sliderControl = function() {
	   		$('#brands-new').trigger( 'nextSlide' );
		  with (this) { setTimeout( function() { sliderControl() }, 1000 );}
	   }
	
	}
	
	sliderControlObjectInstance = new sliderControlObject();
	sliderControlObjectInstance.sliderControl();
});
