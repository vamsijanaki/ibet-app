(function ($) {
    "use strict";

    $(document).ready(function () {

        var wrapper = $('#scrolling-wrapper');
        var leftArrow = $('#left-arrow-container');
        var rightArrow = $('#right-arrow-container');
        var leftOpacityDiv = $('.left-overlay');
        var rightOpacityDiv = $('.right-overlay');
    
        // Check if arrows should be shown initially
        checkArrows();
    
        // Function to check if arrows should be shown
        function checkArrows() {
            var totalWidth = 0;
            wrapper.children('.league-item').each(function() {
                totalWidth += $(this).outerWidth(true); // Include margins
            });

            if (totalWidth > wrapper.innerWidth()) {
                // Items overflow, show arrows
                leftArrow.show();
                rightArrow.show();
                rightOpacityDiv.show();
            } else {
                // Items fit within the wrapper, hide arrows
                leftArrow.hide();
                rightArrow.hide();
                leftOpacityDiv.hide();
                rightOpacityDiv.hide();
            }
        }
    
        // Scroll event listener
        wrapper.scroll(function() {
            if (wrapper.scrollLeft() > 0) {
                leftArrow.show();
                leftOpacityDiv.show();
            } else {
                leftArrow.hide();
                leftOpacityDiv.hide();
            }

            if (wrapper.scrollLeft() + wrapper.innerWidth() >= wrapper[0].scrollWidth - 1) {
                rightArrow.hide();
                rightOpacityDiv.hide();
            } else {
                rightArrow.show();
                rightOpacityDiv.show();
            }
        });
    
        // Click event listener for left arrow
        $('.left-scroll').click(function() {
            wrapper.animate({scrollLeft: '-=200'}, 300);
        });
    
        // Click event listener for right arrow
        $('.right-scroll').click(function() {
            wrapper.animate({scrollLeft: '+=200'}, 300);
        });

        // Update arrows on resize
        $(window).resize(function() {
            checkArrows();
        });

        
        Livewire.on('filterUpdated', function () {
            // Check if arrows should be shown
            setTimeout(function() {
                checkArrows()
            }, 500);
        });


    });

})(jQuery);