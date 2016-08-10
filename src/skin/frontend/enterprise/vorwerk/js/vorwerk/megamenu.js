/* Modernizr 2.0.6 (Custom Build) | MIT & BSD
 * Build: http://www.modernizr.com/download/#-touch-teststyles-prefixes
 */
;window.Modernizr=function(a,b,c){function y(a,b){return!!~(""+a).indexOf(b)}function x(a,b){return typeof a===b}function w(a,b){return v(m.join(a+";")+(b||""))}function v(a){j.cssText=a}var d="2.0.6",e={},f=b.documentElement,g=b.head||b.getElementsByTagName("head")[0],h="modernizr",i=b.createElement(h),j=i.style,k,l=Object.prototype.toString,m=" -webkit- -moz- -o- -ms- -khtml- ".split(" "),n={},o={},p={},q=[],r=function(a,c,d,e){var g,i,j,k=b.createElement("div");if(parseInt(d,10))while(d--)j=b.createElement("div"),j.id=e?e[d]:h+(d+1),k.appendChild(j);g=["&shy;","<style>",a,"</style>"].join(""),k.id=h,k.innerHTML+=g,f.appendChild(k),i=c(k,a),k.parentNode.removeChild(k);return!!i},s,t={}.hasOwnProperty,u;!x(t,c)&&!x(t.call,c)?u=function(a,b){return t.call(a,b)}:u=function(a,b){return b in a&&x(a.constructor.prototype[b],c)};var z=function(c,d){var f=c.join(""),g=d.length;r(f,function(c,d){var f=b.styleSheets[b.styleSheets.length-1],h=f.cssRules&&f.cssRules[0]?f.cssRules[0].cssText:f.cssText||"",i=c.childNodes,j={};while(g--)j[i[g].id]=i[g];e.touch="ontouchstart"in a||j.touch.offsetTop===9},g,d)}([,["@media (",m.join("touch-enabled),("),h,")","{#touch{top:9px;position:absolute}}"].join("")],[,"touch"]);n.touch=function(){return e.touch};for(var A in n)u(n,A)&&(s=A.toLowerCase(),e[s]=n[A](),q.push((e[s]?"":"no-")+s));v(""),i=k=null,e._version=d,e._prefixes=m,e.testStyles=r;return e}(this,this.document);


jQuery.fn.megaMenu = function(menu_effect)
{
	
	var menuItem = $('.megamenu li'),
	    menuItemLink = $(menuItem).find('a'),
	    menuItemChildren = ('.dropcontent, .fullwidth');

	function openCloseMegamenu() {
		$(menuItemLink).click(function() {
			$(this).parent('li').toggleClass('active').siblings().removeClass('active');
			$(menuItemChildren).fadeOut(400, 0);
			$(this).parent('li').children(menuItemChildren).fadeTo(400, 1);
		});
	}  

	$('.dropcontent').css('left', 'auto').hide();
	$('.fullwidth').css('left', '-1px').hide();
	
	if (Modernizr.touch){
		
			$(menuItemLink).bind('touchstart', function() {
				
				var $this = $(this);
					
				$this.parent('li').toggleClass('isvisible');
				if( $this.parent('li').hasClass('isvisible') ) {
					$this.parent('li').removeClass('noactive').siblings().children(menuItemChildren).css("left", "-999em").fadeOut(1);
					if( $this.parent('li').children(menuItemChildren).hasClass('fullwidth') ) {
						$this.parent('li').children(menuItemChildren).css("left", "-1px").fadeTo(1, 1);
					} else {
						$this.parent('li').children(menuItemChildren).css("left", "auto").fadeTo(1, 1);
					}
				} else {
					$this.parent('li').addClass('noactive').children(menuItemChildren).css("left", "-999em").fadeOut(1);
				}
				
			});

			$('.megamenu').bind('touchstart', function(e) {
				e.stopPropagation();
			});
			
			$(document).bind('touchstart', function(){
				$(menuItemChildren).css("left", "-999em");
				$(menuItem).addClass('noactive').removeClass('isvisible');
			});
			
	} else {

	switch( menu_effect )
	{

		case "hover_show":
			$(menuItem).hover(function() {
				$(this).children(menuItemChildren).stop().delay(0).fadeTo(0, 1);
				}, function () { 
				$(this).children(menuItemChildren).stop().fadeTo(0, 0, function() {
				  $(this).hide(); 
			  });
			});
			break;

		case "hover_fade":
			$(menuItem).hover(function() {
				$(this).children(menuItemChildren).stop().delay(200).fadeTo(400, 1);
				}, function () { 
				$(this).children(menuItemChildren).stop().fadeTo(200, 0, function() {
				  $(this).hide(); 
			  });
			});
			break;
	
		case "hover_fadein":
			$(menuItem).hover(function() {
				$(this).children(menuItemChildren).stop().delay(200).fadeTo(400, 1);
				}, function () { 
				$(this).children(menuItemChildren).stop().fadeTo(0, 0).hide();
			});
			break;
	
		case "hover_slide":
			$(menuItem).hover(function() {
				$(this).children(menuItemChildren).delay(200).animate({height: 'toggle'}, 200);
				}, function () { 
				$(this).children(menuItemChildren).animate({height: 'toggle'}, 200);
			});
			break;
	
		case "hover_toggle":
			$(menuItem).hover(function() {
				$(this).children(menuItemChildren).delay(200).toggle(200);
				}, function () { 
				$(this).children(menuItemChildren).toggle(0);
			});
			break;
	
		case "click_fade":
			$(menuItem).click(function() {
				$(this).children(menuItemChildren).fadeIn(400);
				$(this).hover(function() {
				}, function(){	
					$(this).children(menuItemChildren).fadeOut(200);
				});
			});
			break;
	
		case "click_slide":
			$(menuItem).click(function() {
				$(this).children(menuItemChildren).slideDown(200);
				$(this).hover(function() {
				}, function(){	
					$(this).children(menuItemChildren).slideUp(200);
				});
			});
			break;
	
		case "click_toggle":
			$(menuItem).click(function() {
				$(this).children(menuItemChildren).show(200);
				$(this).hover(function() {
				}, function(){	
					$(this).children(menuItemChildren).hide(200);
				});
			});
			break;
	
		case "click_open_close":
			openCloseMegamenu();		
			break;
	
		case "click_open_close_slide":
			$(menuItemLink).click(function() {
				$(this).parent('li').toggleClass('active').siblings().removeClass('active').children(menuItemChildren).slideUp(400);
				$(this).parent('li').children(menuItemChildren).slideToggle(400);
			});
			break;
	
		case "click_open_close_toggle":
			$(menuItemLink).click(function() {
				$(this).parent('li').toggleClass('active').siblings().removeClass('active').children(menuItemChildren).hide(400);
				$(this).parent('li').children(menuItemChildren).toggle(400);
			});
			break;
	
		case "opened_first":
			$('.megamenu li:first-child > div').fadeTo(400, 1).parent().toggleClass('active');
			openCloseMegamenu();		
			break;
	
		case "opened_last":
			$('.megamenu li:last-child > div').fadeTo(400, 1).parent().toggleClass('active');
			openCloseMegamenu();		
			break;
	
		case "opened_second":
			$('.megamenu li:nth-child(2) > div').fadeTo(400, 1).parent().toggleClass('active');
			openCloseMegamenu();		
			break;
	
		case "opened_third":
			$('.megamenu li:nth-child(3) > div').fadeTo(400, 1).parent().toggleClass('active');
			openCloseMegamenu();		
			break;
	
		case "opened_fourth":
			$('.megamenu li:nth-child(4) > div').fadeTo(400, 1).parent().toggleClass('active');
			openCloseMegamenu();		
			break;
	
		case "opened_fifth":
			$('.megamenu li:nth-child(5) > div').fadeTo(400, 1).parent().toggleClass('active');
			openCloseMegamenu();		
			break;
	
		}
	
	}

	
}
