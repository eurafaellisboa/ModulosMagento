define([
    'require',
    'jquery',
    'mage/translate',
    'underscore',
    'matchMedia',
    'Magento_PageBuilder/js/utils/breakpoints',
    'Magento_PageBuilder/js/events',
    'Blueskytechco_PageBuilderCustom/js/resource/packery/packery.pkgd'
], function (require, $, $t, _, mediaCheck, breakpointsUtils, events, Packery) {
    'use strict';
    function buildPackery($element) {
        var $carouselElement = $($element.children().find('.widget-lookbook-wrapper'));
        if($carouselElement.find('.elementor-lookbook-item').length > 5){
            require( [ 'jquery-bridget/jquery-bridget' ], function( jQueryBridget ) {
                jQueryBridget('packery', Packery, $);
                $carouselElement.packery({
                    itemSelector: '.elementor-lookbook-item',
                    columnWidth: '.elementor-lookbook-item',
                    gutter: 0,
                    percentPosition: true,
                    originLeft: true 
                });
            });
        }
    }

    return function (config, element) {
        var $element = $(element);
        buildPackery($element)
    };
});