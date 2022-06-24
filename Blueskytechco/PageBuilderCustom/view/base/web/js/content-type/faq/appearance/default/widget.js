define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var $element = $(element);
        $($element).on("click", '.click-toggle-faqs', function(e) {
            e.preventDefault();
            var _this = $(this),
            parent = _this.closest('.pagebuilder-faq-item'),
            parent_top = _this.closest('.ul-list-faqs'),
            time = 300;
            parent.addClass('clicked_faq');
            if (parent.hasClass('faq-active')) {
                parent.removeClass('faq-active');
                parent.find('.data-content-faqs').slideUp(time);
            }
            else{
                parent_top.find('.pagebuilder-faq-item').removeClass('faq-active');
                parent.addClass('faq-active');
                parent_top.find('.data-content-faqs').slideUp(150);
                parent.find('.data-content-faqs').stop(true, true).slideDown(time);
            }

            return false;
        });
    };
});