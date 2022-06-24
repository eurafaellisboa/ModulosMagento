define([
    'jquery',
    'Blueskytechco_PageBuilderCustom/js/resource/isotope/isotope.pkgd',
    'Blueskytechco_CustomCatalog/js/product/drift.min',
    'Blueskytechco_CustomCatalog/js/product/sticky_sidebar',
    'slick'
], function ($, Isotope) {
    'use strict';

    return function (widget) {

        $.widget('mage.SwatchRenderer', widget, {
            _OnClick: function ($this, $widget) {
                var $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                    $wrapper = $this.parents('.' + $widget.options.classes.attributeOptionsWrapper),
                    $label = $parent.find('.' + $widget.options.classes.attributeSelectedOptionLabelClass),
                    attributeId = $parent.attr('attribute-id'),
                    $input = $parent.find('.' + $widget.options.classes.attributeInput),
                    checkAdditionalData = JSON.parse(this.options.jsonSwatchConfig[attributeId]['additional_data']);

                if ($widget.inProductList) {
                    $input = $widget.productForm.find(
                        '.' + $widget.options.classes.attributeInput + '[name="super_attribute[' + attributeId + ']"]'
                    );
                }

                if ($this.hasClass('disabled')) {
                    return;
                }

                if ($this.hasClass('selected')) {
                    $parent.removeAttr('option-selected').find('.selected').removeClass('selected');
                    $input.val('');
                    $label.text('');
                    $this.attr('aria-checked', false);
                } else {
                    $parent.attr('option-selected', $this.attr('option-id')).find('.selected').removeClass('selected');
                    $label.text($this.attr('option-label'));
                    $input.val($this.attr('option-id'));
                    $input.attr('data-attr-name', this._getAttributeCodeById(attributeId));
                    $this.addClass('selected');
                    $widget._toggleCheckedAttributes($this, $wrapper);
                }

                $widget._Rebuild();

                if ($widget.element.parents($widget.options.selectorProduct)
                        .find(this.options.selectorProductPrice).is(':data(mage-priceBox)')
                ) {
                    $widget._UpdatePrice();
                }

            $(document).trigger('updateMsrpPriceBlock',
                [
                    _.findKey($widget.options.jsonConfig.index, $widget.options.jsonConfig.defaultValues),
                    $widget.options.jsonConfig.optionPrices
                ]);
    
                if (parseInt(checkAdditionalData['update_product_preview_image'], 10) === 1) {
                    $widget._loadMedia();
                    if ($('body').hasClass('product-layout-1') || $('body').hasClass('product-layout-2') || $('body').hasClass('product-layout-3')) {
                        var images = this.options.jsonConfig.images[this.getProduct()];
                        var images_ar = this._sortImages(images);
                        var images_html = '',images_thumb = '',option_id = $this.data('option-id');
                        var images_opstion = option_id+'-'+attributeId;
                        var $productGallery = $('.gallery-images'),
                            gallery = $productGallery.find('.gallery-img'),
                            width = gallery.attr('data-width'),
                            height = gallery.attr('data-height');
                        if (images_ar.length) {
                            images_ar.forEach(function(item) {
                                if (item.img) {
                                    images_html += '<div style="width: 100%;" class="product-image '+images_opstion+' '+images_opstion+'-'+item.position+'">'+
                                        '<div class="gallery-img" style="padding-bottom: '+height/width*100+'%;" data-width="'+width+'" data-height="'+height+'"'+
                                            'data-mdid="'+images_opstion+'-'+item.position+'" data-src="'+item.img+'"'+
                                            'data-bgset="'+item.img+'">'+
                                            '<img class="product-image-photo product-image lazyload" data-src="'+item.img+'" alt="">'+
                                        '</div>'+
                                    '</div>';
                                    images_thumb += '<div class="product-image '+images_opstion+' '+images_opstion+'-'+item.position+'">'+
                                        '<div class="gallery-img" data-width="" data-height="" data-mdid="'+images_opstion+'-'+item.position+'" data-src="'+item.thumb+'">'+
                                            '<img class="product-image" src="'+item.thumb+'" alt="">'+
                                        '</div>'+
                                    '</div>';
                                }
                            });
                            if (!$('.product-image').hasClass(images_opstion)) {
                                var el = $('.gallery-images'),option_isotope = el.attr("data-prmasonry"),
                                    option_slick = el.attr("data-slick"),nav = $('.p-thumb-nav'),option_slick_nav = nav.attr("data-slick");
                                if (el.hasClass('isotope-image')) {
                                    require( [ 'jquery-bridget/jquery-bridget' ], function( jQueryBridget ) {
                                        jQueryBridget('isotope', Isotope, $);
                                        if (el.length > 0){
                                            if ($(window).width() > 768) {
                                                el.isotope('destroy').removeClass('isotope_ok');
                                                el.append(images_html);
                                                el.isotope(JSON.parse(option_isotope)).addClass('isotope_ok');
                                                self.scrollImage(images_opstion);
                                            } else {
                                                if (el.hasClass('slick-initialized')) {
                                                    el.slick('unslick');    
                                                }
                                                el.append(images_html);
                                                if (!el.hasClass('slick-initialized')) {
                                                    el.slick(JSON.parse(option_slick));
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    if (el.hasClass('slick-initialized')) {
                                        el.slick('unslick');    
                                    }
                                    if (nav.hasClass('slick-initialized')) {
                                        nav.slick('unslick');    
                                    }
                                    el.append(images_html);
                                    nav.append(images_thumb);
                                    setTimeout(function() {
                                        el.slick(JSON.parse(option_slick));
                                        nav.slick(JSON.parse(option_slick_nav));
                                    }, 10);
                                }
                            } else {
                                self.scrollImage(images_opstion);
                            }
                            setTimeout(function() {
                                if ($('.gallery-images').hasClass('slick-initialized')) {
                                    $('.gallery-images').find('.slick-slide').removeClass('is-selected');
                                    $('.p-thumb-nav').find('.slick-slide').removeClass('is-selected');
                                    var nextSlide = $('.p-thumb-nav').find('.product-image.'+images_opstion+'').closest('.slick-slide').attr('data-slick-index');
                                    $('.p-thumb-nav').slick('slickGoTo', nextSlide);
                                    $('.gallery-images').slick('slickGoTo', nextSlide);
                                    $('.p-thumb-nav').find('.slick-slide[data-slick-index="'+nextSlide+'"]').addClass('is-selected');
                                }
                                var $productGallery = $('.gallery-images');
                                if ($productGallery.hasClass('img_action_zoom') && $(window).width() > 1025 ) {
                                    var p_thumb = $('.p-thumb'),
                                        p_infors = $('.product-infors'),
                                        zoom_target = $('.p-thumb .gallery-img'),
                                        dt_zoom_img = $('.dt_img_zoom')[0],
                                        zoom_tp = $productGallery.data('zoom'),
                                        z_magnify = 2;
                                    if ($('body').hasClass('product-layout-1') && zoom_tp == 1) {
                                        zoom_tp = 2;
                                    } else if (($('body').hasClass('product-layout-2') || $('body').hasClass('product-layout-3')) && zoom_tp == 2) {
                                        zoom_tp = 1;
                                    }
                                    $('body').addClass('zoom_tp_'+zoom_tp+'');
                                    zoom_target.each(function () {
                                        var $this = $(this),
                                            _this = $this[0];
                                        if (!$this.hasClass('zoom_ok')) {
                                            $this.addClass('zoom_ok');
                                            new Drift(_this, {
                                                sourceAttribute: 'data-src',
                                                paneContainer: zoom_tp == '2' ? dt_zoom_img : _this,
                                                zoomFactor: z_magnify,
                                                inlinePane: zoom_tp == '3',
                                                hoverBoundingBox: zoom_tp == '2',
                                                handleTouch: false,
                                                onShow: function onShow() {
                                                    p_thumb.addClass('zoom_fade_ic');
                                                    p_infors.addClass('zoom_fade_if');
                                                },
                                                onHide: function onHide() {
                                                    p_thumb.removeClass('zoom_fade_ic');
                                                    p_infors.removeClass('zoom_fade_if');
                                                } 
                                            });   
                                        }
                                    });
                                }
                            }, 500);
                        }
                        if ( $('body').hasClass('sticky-sidebar-enable') && !$('body').hasClass('product-layout-1') && $(window).width() > 768 ){
                            var offset = 20,sticky,
                                $img = $('.pr_sticky_img'),
                                $info = $('.pr_sticky_info'),
                                img_h = $img.find('.theiaStickySidebar').outerHeight(),
                                info_h = $info.find('.theiaStickySidebar').outerHeight();
                            if ( img_h > info_h ) {
                                sticky = $info;
                            } else if ( img_h == null ) {
                                sticky = $info;
                            } else {
                                sticky = $img;
                            }
                            if ($('.sticky-header').length) {
                                offset = $('.sticky-header').outerHeight() + 20;
                            }
                            sticky.addClass('is_sticky').theiaStickySidebar({
                                additionalMarginTop: offset,
                                additionalMarginBottom: 20,
                                minWidth: 768
                            });
                        }
                    }
                }
                $input.trigger('change');
            },
            scrollImage: function (images_opstion) { 
                if ( !$('body').hasClass('product-layout-1') ){
                    $("html, body").animate({ scrollTop: $("."+images_opstion+"").offset().top }, 1000);
                }
            }
        });

        return $.mage.SwatchRenderer;
    }
});