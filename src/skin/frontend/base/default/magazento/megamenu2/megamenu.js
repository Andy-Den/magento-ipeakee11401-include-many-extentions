// jQuery Mega Menu Effects

// To apply one of those effects (replace "hover_fade" by any other effect) :
// JQ(function() {
//	 JQ("#menu").megaMenu('hover_fade');
// });

var JQ = jQuery.noConflict();

 //JQ(
 //   function() {
 //	JQ("#menu").megaMenu('hover_fade');
 //   }
 //   );

jQuery.fn.megaMenu = function(menu_effect) {

	JQ(".dropcontent").css('left', 'auto').hide();
	JQ(".fullwidth").css('left', '-1px').hide();
	
	switch( menu_effect )
	{

	case "hover_fade":
		JQ('.mm2').hover(function() {
			JQ(this).children().stop().fadeTo(400, 1);
			}, function () { 
			JQ(this).children("div").stop().fadeTo(400, 0, function() {
			  JQ(this).hide();
		  });
		});
		break;

	case "hover_fadein":
		JQ('.mm2').hover(function() {
			JQ(this).children().stop().fadeTo(400, 1).show();
			}, function () { 
			JQ(this).children("div").stop().hide();
		});
		break;

	case "hover_slide":
		JQ('.mm2').hover(function() {
			var JQthis = JQ(this);
			JQthis.children("div").slideDown('fast');
			JQthis.hover(function() {
			}, function(){	
				JQ(this).children("div").slideUp(200);
			});
		});
		break;

	case "hover_toggle":
		JQ('.mm2').hover(function() {
			JQ(this).children("div").toggle('fast').show();
		});
		break;

	case "click_fade":
		JQ('.mm2').click(function() {
			var JQthis = JQ(this);
			JQthis.children().fadeIn(400).show();
			JQthis.hover(function() {
			}, function(){	
				JQthis.children("div").fadeOut(400);
			});
		});
		break;

	case "click_slide":
		JQ('.mm2').click(function() {
			var JQthis = JQ(this);
			JQthis.children().slideDown('fast').show();
			JQthis.hover(function() {
			}, function(){	
				JQthis.children("div").slideUp('slow');
			});
		});
		break;

	case "click_toggle":
		JQ('.mm2').click(function() {
			var JQthis = JQ(this);
			JQthis.children("div").toggle('fast').show();
			JQthis.hover(function() {
			}, function(){	
				JQthis.children("div").hide('slow');
			});
		});
		break;

	case "click_open_close":
		JQ('.mm2').click(function() {
			var JQthis = JQ(this);
			JQthis.toggleClass('active');
			JQthis.siblings().removeClass('active');
			JQ(".dropcontent, .fullwidth").fadeOut(400, 0);
			JQ(this).children().fadeTo(400, 1);
		});
		break;

	case "opened_first":
		JQ("li:first-child > div").fadeTo(400, 1).parent().toggleClass('active');
		JQ("li").click(function() {
			var JQthis = JQ(this);
			JQthis.toggleClass('active');
			JQthis.siblings().removeClass('active');
			JQ(".dropcontent, .fullwidth").fadeOut(400, 0);
			JQthis.find(".dropcontent, .fullwidth").fadeTo(400, 1);
		});
		break;

	case "opened_last":
		JQ("li:last-child > div").fadeTo(400, 1).parent().toggleClass('active');
		JQ("li").click(function() {
			var JQthis = JQ(this);
			JQthis.toggleClass('active');
			JQthis.siblings().removeClass('active');
			JQ(".dropcontent, .fullwidth").fadeOut(400, 0);
			JQthis.find(".dropcontent, .fullwidth").fadeTo(400, 1);
		});
		break;

	case "opened_second":
		JQ("li:nth-child(2) > div").fadeTo(400, 1).parent().toggleClass('active');
		JQ("li").click(function() {
			var JQthis = JQ(this);
			JQthis.toggleClass('active');
			JQthis.siblings().removeClass('active');
			JQ(".dropcontent, .fullwidth").fadeOut(400, 0);
			JQthis.find(".dropcontent, .fullwidth").fadeTo(400, 1);
		});
		break;

	case "opened_third":
		JQ("li:nth-child(3) > div").fadeTo(400, 1).parent().toggleClass('active');
		JQ("li").click(function() {
			var JQthis = JQ(this);
			JQthis.toggleClass('active');
			JQthis.siblings().removeClass('active');
			JQ(".dropcontent, .fullwidth").fadeOut(400, 0);
			JQthis.find(".dropcontent, .fullwidth").fadeTo(400, 1);
		});
		break;

	case "opened_fourth":
		JQ("li:nth-child(4) > div").fadeTo(400, 1).parent().toggleClass('active');
		JQ("li").click(function() {
			var JQthis = JQ(this);
			JQthis.toggleClass('active');
			JQthis.siblings().removeClass('active');
			JQ(".dropcontent, .fullwidth").fadeOut(400, 0);
			JQthis.find(".dropcontent, .fullwidth").fadeTo(400, 1);
		});
		break;

	case "opened_fifth":
		JQ("li:nth-child(5) > div").fadeTo(400, 1).parent().toggleClass('active');
		JQ("li").click(function() {
			var JQthis = JQ(this);
			JQthis.toggleClass('active');
			JQthis.siblings().removeClass('active');
			JQ(".dropcontent, .fullwidth").fadeOut(400, 0);
			JQthis.find(".dropcontent, .fullwidth").fadeTo(400, 1);
		});
		break;

	
	}

	
}
