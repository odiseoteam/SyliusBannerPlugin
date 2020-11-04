document.addEventListener("DOMContentLoaded", function(event) {
    const glide = new Glide('#banner-slider', {
        autoplay: 5000,
        hoverpause: false,
        type: 'carousel',
    });

    glide.mount();
});
