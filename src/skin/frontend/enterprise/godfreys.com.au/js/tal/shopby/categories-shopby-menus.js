$j1_6(function() {
    var $ = $j1_6;
    $('#categories_list h3').click(function() {
        $(this).next().slideToggle('fast');
        $(this).toggleClass('active');
        return false;
    }).next().hide();

    $('#categories_list h3').first().toggleClass('active');

    $('#categories_list ul').first().show();

    $('dl#narrow-by-list dt').click(function() {
        $(this).next().slideToggle('fast');
        $(this).toggleClass('active');
        return false;
    }).next().hide();

    $('dl#narrow-by-list dt').first().toggleClass('active');

    $('dl#narrow-by-list dd').first().show();

    // VAC FINDER (FK)
    $('a#next-step').click(function() {

        // CALCULATE nth-child //
        var count = 2 * ($j1_6(this).parents('div.dark').index('div.dark') + 2);
		if ($('div.dark:nth-child(' + count + ')').css('display') != 'none') {
		$('div.dark:nth-child(' + count + ')').effect("highlight", {}, 3000);
		}
		$('div.dark:nth-child(' + count + ')').slideDown();
        $(this).fadeOut('slow');
    })
});