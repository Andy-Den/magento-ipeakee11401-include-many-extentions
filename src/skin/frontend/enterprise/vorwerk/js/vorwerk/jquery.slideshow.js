$(function() {
    function changeSlide( newSlide ) {        
        // change the currSlide value
        currSlide = newSlide;
        
        // make sure the currSlide value is not too low or high
        if ( currSlide > maxSlide ) currSlide = 0;
        else if ( currSlide < 0 ) currSlide = maxSlide;
        
        // animate the slide reel
        $slideReel.animate({
            left : currSlide * -960
        }, 400, 'swing', function() {
            // set new timeout if active
            if ( activeSlideshow ) slideTimeout = setTimeout(nextSlide, 7000);
        });
    }
    
    function nextSlide() {
        changeSlide( currSlide + 1 );
    }
    
    // define some variables / DOM references
    var activeSlideshow = true,
    currSlide = 0,
    slideTimeout,
    $slideshow = $('#slideshow'),
    $slideReel = $slideshow.find('#slideshow-reel'),
    maxSlide = $slideReel.children().length - 1;
    
    // start the animation
    slideTimeout = setTimeout(nextSlide, 7000);
});