(function($) {
    $(document).ready(function () {
        var glide = new Glide('#banner-slider', {
            autoplay: 5000,
            hoverpause: false,
            type: 'carousel',
        });

        glide.mount();
    });
})(jQuery);
