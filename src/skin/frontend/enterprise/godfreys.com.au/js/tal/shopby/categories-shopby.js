$j1_6(function(){
var $ = $j1_6; 
	$( "#slider-range" ).slider(

		{
		range: true,
		min: 0,
		max: 2000,
		step: 10,
		values: [ 200, 1500 ],
		slide: function( event, ui ) {
			$("#amount-left" ).val( "$" + ui.values[ 0 ]);
			$("#amount-right" ).val( "$" + ui.values[ 1 ]);
			
		}
	});
$( "#amount-left" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ));
$( "#amount-right" ).val("$" + $( "#slider-range" ).slider( "values", 1 ) );
});