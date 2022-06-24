define([
    'require',
    'jquery',
    'mage/translate',
    'underscore',
    'matchMedia',
    'Magento_PageBuilder/js/utils/breakpoints',
    'Magento_PageBuilder/js/events',
    'blueskytechco/photoswipe',
    'blueskytechco/photoswipe_ui',
    'Blueskytechco_PageBuilderCustom/js/resource/packery/packery.pkgd',
    'slick'
], function (require, $, $t, _, mediaCheck, breakpointsUtils, events, PhotoSwipe, PhotoSwipeUI_Default, Packery) {
    'use strict';

    function getGalleryItemImages($ele) {
        var items = [];
        $ele.each(function () {
            items.push({
                src: $(this).attr('data-bgset'),
                w: $(this).attr('data-width'),
                h: $(this).attr('data-height')
            });
        });
        return items;
    }

    function callGalleryPhotoSwipeAllItems(index, items) {
        var $pswp = $('.pswp')[0],
        options = {
            history: false,
            focus: false,
            showHideOpacity: true,
            bgOpacity:1,
            index: index,
            shareButtons: [
                {id: 'facebook', label: $t('Share on Facebook'), url:'https://www.facebook.com/sharer/sharer.php?u={{url}}' },
                {id: 'twitter', label: $t('Tweet'), url:'https://twitter.com/intent/tweet?text={{text}}&url={{url}}'},
                {id: 'pinterest', label: $t('Pin it'), url:'http://www.pinterest.com/pin/create/button/?url={{url}}&media={{image_url}}&description={{text}}'},
                {id: 'download', label: $t('Download image'), url:'{{raw_image_url}}', download:true}
            ]
        };
        var gallery = new PhotoSwipe($pswp, PhotoSwipeUI_Default, items, options);
        gallery.init();
    }

    function buildSlick($carouselElement, config) {
        if ($carouselElement.hasClass('slick-initialized')) {
            $carouselElement.slick('unslick');
        }
        $carouselElement.slick(config);
    }

    function getCol($col) {
        if($col == '12'){
            return 1;
        }
        else if($col == '6'){
            return 2;
        }
        else if($col == '4'){
            return 3;
        }
        else if($col == '3'){
            return 4;
        }
        else if($col == '15'){
            return 5;
        }
        else if($col == '2'){
            return 6;
        }
        return 5;
    }

    function initSlider($element) {
        var col_xl = getCol($element.data('col-xl')),
            col_lg = getCol($element.data('col-lg')),
            col_md = getCol($element.data('col-md')),
            col_sm = getCol($element.data('col-sm')),
            col_xs = getCol($element.data('col-xs')),
            col_xxl = getCol($element.data('col-xxl'));
        var centerModeClass = 'center-mode',
            itemCount = $element.find('.elementor-img-item').length,
            carouselMode = $element.data('carousel-mode'),
            $carouselElement = $($element.find('.page-builder-gallery-container')),
            slickConfig = {
                slidesToShow: col_xxl,
                slidesToScroll: col_xxl,
                rows: $element.data('slick-rows') || 1,
                autoplay: $element.data('autoplay'),
                autoplaySpeed: $element.data('autoplay-speed') || 0,
                arrows: $element.data('show-arrows'),
                dots: $element.data('show-dots'),
                responsive: [
                    {
                        breakpoint: 1400,
                        settings: {
                            slidesToShow: col_xl,
                            slidesToScroll: col_xl
                        }
                    },
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: col_lg,
                            slidesToScroll: col_lg
                        }
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: col_md,
                            slidesToScroll: col_md
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: col_sm,
                            slidesToScroll: col_sm
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: col_xs,
                            slidesToScroll: col_xs
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
        var dataLayoutDesign = $element.data('layout-design');
    
        if(dataLayoutDesign == 'packery'){
            var $galleryContainer = $($element.children());
            require( [ 'jquery-bridget/jquery-bridget' ], function( jQueryBridget ) {
                jQueryBridget('packery', Packery, $);
                $galleryContainer.packery({
                    itemSelector: '.elementor-img-item',
                    columnWidth: '.elementor-img-item',
                    gutter: 0,
                    percentPosition: true,
                    originLeft: true 
                });
            });
        }
        else if(dataLayoutDesign == 'carousel'){
            var $galleryContainer = $($element);
            $galleryContainer.find('.page-builder-gallery-container').removeClass('row');
            var $elementorImgItem = $galleryContainer.find('.elementor-img-item');
            $elementorImgItem.attr('class', '');
            $elementorImgItem.attr('class', 'elementor-img-item');

            var $carouselElement = $($element.find('.page-builder-gallery-container'));
            initSlider($element);

            events.on('contentType:redrawAfter', function (args) {
                if ($carouselElement.closest(args.element).length) {
                    $carouselElement.slick('refresh');
                    $carouselElement.slick('setPosition');
                }
            });
        }
        
        $($element).on("click", '.item-img-click-event', function(e) {
            var actionClick = $(this).closest('.pagebuilder-gallery').data('action-click');
            if(actionClick == 'photo_swipe'){
                e.preventDefault();
                var $gallery_items = $(this).closest('.pagebuilder-gallery').find('.item-img-click-event');
                var items = getGalleryItemImages($gallery_items);
                var $parent = $(this).parents('.elementor-img-item')
                var getIndex = $parent.index();
                callGalleryPhotoSwipeAllItems(getIndex, items);
                return false;
            }
        });
    };
});