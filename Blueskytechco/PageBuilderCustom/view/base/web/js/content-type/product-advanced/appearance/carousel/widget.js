define([
    'jquery',
    'underscore',
    'matchMedia',
    'Magento_PageBuilder/js/utils/breakpoints',
    'Magento_PageBuilder/js/events',
    'slick'
], function ($, _, mediaCheck, breakpointsUtils, events) {
    'use strict';

    function buildSlick($carouselElement, config) {
        if ($carouselElement.hasClass('slick-initialized')) {
            $carouselElement.slick('unslick');
        }
        $carouselElement.slick(config);
    }

    function initSlider($element) {
        var centerModeClass = 'center-mode',
            itemCount = $element.find('.product-item').length,
            carouselMode = $element.data('carousel-mode'),
            $carouselElement = $($element.children().find('.product-items')),
            slickConfig = {
                slidesToShow: parseFloat($element.data('col-xxl')),
                slidesToScroll: parseFloat($element.data('col-xxl')),
                rows: $element.data('slick-rows') || 1,
                autoplay: $element.data('autoplay'),
                autoplaySpeed: $element.data('autoplay-speed') || 0,
                arrows: $element.data('show-arrows'),
                dots: $element.data('show-dots'),
                responsive: [
                    {
                        breakpoint: 1400,
                        settings: {
                            slidesToShow: parseFloat($element.data('col-xl')),
                            slidesToScroll: parseFloat($element.data('col-xl'))
                        }
                    },
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: parseFloat($element.data('col-lg')),
                            slidesToScroll: parseFloat($element.data('col-lg'))
                        }
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: parseFloat($element.data('col-md')),
                            slidesToScroll: parseFloat($element.data('col-md'))
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: parseFloat($element.data('col-sm')),
                            slidesToScroll: parseFloat($element.data('col-sm'))
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: parseFloat($element.data('col-xs')),
                            slidesToScroll: parseFloat($element.data('col-xs'))
                        }
                    }
                ]
            };

        if (carouselMode === 'continuous' && itemCount > parseFloat($element.data('col-xl'))) {
            $element.addClass(centerModeClass);
            slickConfig.centerPadding = $element.data('center-padding');
            slickConfig.centerMode = true;
        } else {
            $element.removeClass(centerModeClass);
            slickConfig.infinite = $element.data('infinite-loop');
        }

        buildSlick($carouselElement, slickConfig);
    }

    return function (config, element) {
        var $element = $(element);
        var $carouselElement = $($element.children().find('.product-items'));
        initSlider($element);

        events.on('contentType:redrawAfter', function (args) {
            if ($carouselElement.closest(args.element).length) {
                $carouselElement.slick('refresh');
                $carouselElement.slick('setPosition');
            }
        });
    };
});