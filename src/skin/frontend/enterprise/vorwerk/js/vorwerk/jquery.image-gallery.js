/* -------------------------------------------------------------
 *	Image Gallery
 * ------------------------------------------------------------- */
jQuery.fn.imagegallery = function(options) {
	if(typeof window.imagegalleries == 'undefined'){
		window.imagegalleries = [];		
	}
	jQuery('.image-gallery').each(function(i,gallery){
		if (!jQuery(gallery).is('.initated') && (jQuery(gallery).parent().parent().attr("id") != "lightboxSCS")) {
			
			// Initially set opacity on thumbs and add
			// additional styling for hover effect on thumbs
			jQuery(gallery).addClass('initated')
			jQuery(gallery).addClass('gallery-'+ i)
			jQuery(gallery).attr('rel', window.imagegalleries.length)
			var newgallery = {gallery: null, markup: jQuery(gallery)}
			window.imagegalleries.push(newgallery)
			
			jQuery(gallery).find('.gallery-content').css('display', 'block');
			var onMouseOutOpacity = 0.6;
			jQuery(gallery).find('.navigation ul.thumbs li').opacityrollover({
				mouseOutOpacity:   onMouseOutOpacity,
				mouseOverOpacity:  1.0,
				fadeSpeed:         'fast',
				exemptionSelector: '.selected'
			});
			
			// Initialize Advanced Galleriffic Gallery
			newgallery.gallery = jQuery(gallery).find('.navigation').galleriffic({
				delay:                     4000,
				numThumbs:                 8,
				preloadAhead:              0,
				enableTopPager:            false,
				enableBottomPager:         true,
				maxPagesToShow:            6,
				imageContainerSel:         '.gallery-'+ i +' .slideshow',
				controlsContainerSel:      '.gallery-'+ i +' .controls',
				captionContainerSel:       '.gallery-'+ i +' .caption-container',
				loadingContainerSel:       '.gallery-'+ i +' .loader',
				renderSSControls:          false, // play/pause
				renderNavControls:         false, // prev/next photo
				playLinkText:              'Play Slideshow',
				pauseLinkText:             'Pause Slideshow',
				prevLinkText:              '&lsaquo;',
				nextLinkText:              '&rsaquo;',
				nextPageLinkText:          '&rsaquo;&rsaquo;',
				prevPageLinkText:          '&lsaquo;&lsaquo;',
				enableHistory:             false,
				enableKeyboardNavigation:  false, // Specifies whether keyboard navigation is enabled
				autoStart:                 true,
				syncTransitions:           false,
				defaultTransitionDuration: 800,
				onSlideChange:             function(prevIndex, nextIndex) {
					// 'this' refers to the gallery, which is an extension of jQuery('#gallery-thumbs')
					this.find('ul.thumbs').children()
						.eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
						.eq(nextIndex).fadeTo('fast', 1.0);
				},
				onPageTransitionOut:       function(callback) {
					this.fadeTo('fast', 0.0, callback);
				},
				onPageTransitionIn:        function() {
					this.fadeTo('fast', 1.0);
				}
			});
		};
	})
	
};