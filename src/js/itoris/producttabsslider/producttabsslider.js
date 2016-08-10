
	Event.observe(document, 'dom:loaded', initializeTabsSlider);
	
	function initializeTabsSlider(){
	
		if (window.opera && window.opera.version() < 10){
		   document.documentElement.className += ' opera9';
		}

		if(iTabsSlider.checkScroolNeeds()){
			iTabsSlider.activateScroll();
		}
		iTabsSlider.initialized = true;
	}
	
	Event.observe(window, 'load', function(event){
		if(!iTabsSlider.initialized){
			initializeTabsSlider();
		}
		if(!iTabsSlider.checkScroolNeeds() && $$('.itabs .i_titles')[0]){
			$$('.itabs .i_titles')[0].removeClassName('with-scroll');
		}
	});
	
	function showWidth(){
		var holder = $$('.itabs .i_titles-holder')[0];
		var scrollWidth = holder.scrollWidth;
		var offsetWidth = holder.offsetWidth;
		document.title = scrollWidth + ' ' + offsetWidth;
		window.setTimeout('showWidth()', 10);
	}
	
	function ITabsSlider(){
		this.initialized = false;
		this.scrollSpeed = 1;
		this.defaultScrollSpeed = 1;
		this.scrollAcceleration = 1.15;
		this.scrollDelay = 15;
		this.scrollDirection = 0;
	}
	
	ITabsSlider.prototype.checkScroolNeeds = function(){
		var holder = $$('.itabs .i_titles-holder')[0];
		if (!holder) {
			return false;
		}
		
		var scrollWidth = holder.scrollWidth;
		var offsetWidth = holder.offsetWidth;
		return scrollWidth > offsetWidth;
	};
	
	ITabsSlider.prototype.activateScroll = function(){
		$$('.itabs .i_titles')[0].addClassName('with-scroll');
		$$('.itabs .i_titles .i_scroll.i__left')[0].observe('mouseover', function(event){
			iTabsSlider.scrollToLeft();
		});
		
		$$('.itabs .i_titles .i_scroll.i__left')[0].observe('mouseout', function(event){
			iTabsSlider.scrollStop();
		});
		
		$$('.itabs .i_titles .i_scroll.i__right')[0].observe('mouseover', function(event){
			iTabsSlider.scrollToRight();
		});
		
		$$('.itabs .i_titles .i_scroll.i__right')[0].observe('mouseout', function(event){
			iTabsSlider.scrollStop();
		});
	};
	
	ITabsSlider.prototype.scrollToLeft = function(){
		this.scrollDirection = -1;
		this.scrollStart();
	};
	
	ITabsSlider.prototype.scrollToRight = function(){
		this.scrollDirection = 1;
		this.scrollStart();
	};
	
	ITabsSlider.prototype.scrollStop = function(){
		this.scrollDirection = 0;
		this.scrollSpeed = this.defaultScrollSpeed;
	};
	
	ITabsSlider.prototype.scrollStart = function(){
		this.scroll();
	};
	
	ITabsSlider.prototype.scroll = function(){
		if(this.scrollDirection == 0) return;
		var titlesHolder = $$('.itabs .i_titles-holder')[0];
		titlesHolder.scrollLeft += this.scrollDirection * this.scrollSpeed;
		this.scrollSpeed = this.scrollSpeed * this.scrollAcceleration;
		if(this.scrollDirection == -1 && titlesHolder.scrollLeft <= 0){
			return;
		}
		if(this.scrollDirection == 1 && titlesHolder.scrollLeft 
				>= titlesHolder.scrollWidth - titlesHolder.offsetWidth){
			return;
		}
		window.setTimeout(function(){this.scroll();}.bind(this),this.scrollDelay);
	};
	
	iTabsSlider = new ITabsSlider();