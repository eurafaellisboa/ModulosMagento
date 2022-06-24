define([
    'jquery',
    'underscore',
    'matchMedia',
    'Magento_PageBuilder/js/utils/breakpoints',
    'Magento_PageBuilder/js/events',
    'slick', 
    "countdown"
], function ($, _, mediaCheck, breakpointsUtils, events) {
    'use strict';

    function buildSlick($carouselElement, config) {
        if ($carouselElement.hasClass('slick-initialized')) {
            $carouselElement.slick('unslick');
        }
        $carouselElement.slick(config);
    }

    function initSectionCountdown($ele) {
        var $countdownItems = $ele.find('[data-countdown]');
        var $thisCountdown = $countdownItems, finalDate = $countdownItems.data('countdown');
        $thisCountdown.countdown(finalDate, function(event) {
            $thisCountdown.closest('.header-daily-deal-container').find('.final-date-daily-deal').html(event.strftime(''
             + '<span class="countdown-days"><span class="countdown_ti">%-D</span> <span class="countdown_tx">'+$ele.data('countdown-text-day')+'</span></span> '
             + '<span class="countdown-hours"><span class="countdown_ti">%H</span> <span class="countdown_tx">'+$ele.data('countdown-text-hour')+'</span></span> '
             + '<span class="countdown-min"><span class="countdown_ti">%M</span> <span class="countdown_tx">'+$ele.data('countdown-text-minute')+'</span></span> '
             + '<span class="countdown-sec"><span class="countdown_ti">%S</span> <span class="countdown_tx">'+$ele.data('countdown-text-second')+'</span></span>'));
        });
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
        initSectionCountdown($element);
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