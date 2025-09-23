'use strict';
setTimeout(function () {

    var slider = tns({
        container: '.slider1',
        items: 1,
        slideBy: 'page',
        autoplay: true
    });

    var slider = tns({
        container: '.slider2',
        items: 1,
        axis: "vertical",
        slideBy: 'page',
        autoplay: true
    });

    var slider = tns({
        container: '#customize',
        items: 3,
        center: true,
        gutter: 10,
        controlsContainer: '#customize-controls',
        navContainer: '#customize-thumbnails',
        navAsThumbnails: true,
        autoplay: true,
        autoplayTimeout: 1000,
        autoplayButton: '#customize-toggle',
    });
});