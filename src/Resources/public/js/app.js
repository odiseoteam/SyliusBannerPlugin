(function($) {
    $(document).ready(function () {
        var isSliderPlusOne = $('#banner-slider .sp-slide').length > 1;
        var isSliderPlusTwo = $('#banner-slider .sp-slide').length > 2;

        $('#banner-slider').sliderPro({
            width: 1127, height: 300,
            fade: false, arrows: true, buttons: false,
            touchSwipe: isSliderPlusOne, autoplayDelay: 5000, keyboardOnlyOnFocus: true,
            loop: isSliderPlusTwo,
            breakpoints: {
                767: { fadeArrows: false }
            }
        });
    });
})(jQuery);
