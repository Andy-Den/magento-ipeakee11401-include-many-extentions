$j1_6(function(){
	var $ = $j1_6;
	$('#tabs li a').click(function(){
		$('#tabs li a').removeClass('active');
		
		var c = $(this).attr('class');
		$('#tab-panes .tab-pane').hide();
		$('#tab-panes .'+c).fadeIn();
				
		$(this).addClass('active');
				
		return false;
	});
	$('#tabs').find('li:first').find('a:first').click();
});
