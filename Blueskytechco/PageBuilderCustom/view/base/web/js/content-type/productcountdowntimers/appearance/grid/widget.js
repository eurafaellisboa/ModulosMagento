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

    function initSectionCountdown($ele, $element) {
        var $countdownItems = $ele.find('[data-countdown]');
        $countdownItems.each(function() {
            var $thisCountdown = $(this), finalDate = $(this).data('countdown');
            $thisCountdown.countdown(finalDate, function(event) {
                $thisCountdown.closest('.product-item').find('.final-date-product-countdown-timers').html(event.strftime(''
                 + '<span class="countdown-days"><span class="countdown_ti">%-D</span> <span class="countdown_tx">'+$element.data('countdown-text-day')+'</span></span> '
                 + '<span class="countdown-hours"><span class="countdown_ti">%H</span> <span class="countdown_tx">'+$element.data('countdown-text-hour')+'</span></span> '
                 + '<span class="countdown-min"><span class="countdown_ti">%M</span> <span class="countdown_tx">'+$element.data('countdown-text-minute')+'</span></span> '
                 + '<span class="countdown-sec"><span class="countdown_ti">%S</span> <span class="countdown_tx">'+$element.data('countdown-text-second')+'</span></span>'));
            });
        });
    }

    return function (config, element) {
        var $element = $(element);

        initSectionCountdown($($element.children().find('.widget-product-countdown-timers-grid')), $element);
    };
});