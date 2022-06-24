define([
    'jquery',
    'mage/translate',
    'Blueskytechco_PageBuilderCustom/js/resource/isotope/isotope.pkgd',
    'blueskytechco/photoswipe',
    'blueskytechco/photoswipe_ui',
    'Blueskytechco_QuickviewProduct/js/model/jquery.magnific-popup.min',
    'Blueskytechco_CustomCatalog/js/product/drift.min',
    'Blueskytechco_CustomCatalog/js/product/sticky_sidebar',
    'slick'
], function ($, $t, Isotope, PhotoSwipe, PhotoSwipeUI_Default, magnificPopup) { 
    'use strict';
    $.widget('mage.customLayout', {
        _create: function () {
            var $this = this;
            $this.productImages();
            $this.productGalleryProduct();
            $this.stickyAddToCartToggle();
            $this.productSticky();
            $this.stickyButtonAddToCartToggle();
            $this.productTabDetailed();
            $this.productInStockNotification();
            $(window).scroll(function() {
                $this.stickyAddToCartToggle()
            });
        },
        productSticky: function () {
            if ( !$('body').hasClass('sticky-sidebar-enable') || $(window).width() < 768 ) return;
            var offset = 20,el,
                $img = $('.pr_sticky_img'),
                $info = $('.pr_sticky_info'),
                img_h = $img.find('.theiaStickySidebar').outerHeight(),
                info_h = $info.find('.theiaStickySidebar').outerHeight();
            if ( img_h > info_h ) {
                el = $info;
            } else if ( img_h == null ) {
                el = $info;
            } else {
                el = $img;
            }
            if ($('.sticky-header').length) {
                offset = $('.sticky-header').outerHeight() + 20;
            }
            el.addClass('is_sticky').theiaStickySidebar({
                additionalMarginTop: offset,
                additionalMarginBottom: 20,
                minWidth: 768
            });
      
        },
        stickyAddToCartToggle: function () { 
            var $trigger = $('#product_addtocart_form');
            var $sticky = $('#sticky-addcart');
            if ($sticky.length <= 0) return;
            var summaryOffset = $trigger.offset().top + $trigger.outerHeight(),
                $selector = $('#sticky-addcart, #back-top'),
                $window = $(window),
                $document = $(document);
            var windowScroll = $window.scrollTop(),
                windowHeight = $window.height(),
                documentHeight = $document.height(),
                totalScroll = parseInt(windowScroll + windowHeight) + 150;
            if (summaryOffset < windowScroll && totalScroll !== documentHeight && totalScroll < documentHeight) {
                $selector.addClass('sticky_atc_shown');
            } else if (totalScroll === documentHeight || totalScroll > documentHeight || summaryOffset > windowScroll) {
                $selector.removeClass('sticky_atc_shown');
            }
        },
        productGalleryProduct: function () {
            var el = $('.gallery-images'),option_isotope = el.attr("data-prmasonry"),
                    option_slick = el.attr("data-slick"),nav = $('.p-thumb-nav'),option_slick_nav = nav.attr("data-slick");
            if (!el.length) {
                return false;
            }
            if (el.hasClass('isotope-image')) {
                require( [ 'jquery-bridget/jquery-bridget' ], function( jQueryBridget ) {
                    jQueryBridget('isotope', Isotope, $);
                    if (el.length > 0){
                        if ($(window).width() > 768) {
                            setTimeout(function() {
                                el.isotope(JSON.parse(option_isotope));
                            }, 200);    
                        } else {
                            if (!el.hasClass('slick-initialized')) {
                                el.slick(JSON.parse(option_slick));
                            }
                        }
                    }
                    $(window).on('resize', function () {
                        if ($(window).width() < 768 && el.hasClass('isotope_ok') ) {
                            el.isotope('destroy').removeClass('isotope_ok'); 
                            if (!el.hasClass('slick-initialized')) {
                                el.slick(JSON.parse(option_slick));
                            }
                        } else if ($(window).width() >= 768 && !el.hasClass('isotope_ok') ) {
                            if (el.hasClass('slick-initialized')) {
                                el.slick('unslick');    
                            }
                            el.isotope(JSON.parse(option_isotope)).addClass('isotope_ok'); 
                        }
                    });
                });
            } else {
                el.not('.slick-initialized').slick(JSON.parse(option_slick));
                nav.not('.slick-initialized').slick(JSON.parse(option_slick_nav));
            }
            if (el.hasClass('slick-initialized')) {
                el.on('beforeChange', function(event, slick, currentSlide, nextSlide){
                    nav.slick('slickGoTo', nextSlide);
                    $('.p-thumb-nav .slick-slide').removeClass('is-selected');
                    $('.p-thumb-nav .slick-slide[data-slick-index="'+nextSlide+'"]').addClass('is-selected');
                });
                if (nav.hasClass('slick-initialized')) {
                    $('.p-thumb-nav .slick-slide[data-slick-index="0"]').addClass('is-selected');
                    $(document).on('click', '.p-thumb-nav .slick-slide', function() {
                        var slickIndex = $(this).attr('data-slick-index');
                        el.slick('slickGoTo', slickIndex);
                        $('.p-thumb-nav .slick-slide').removeClass('is-selected');
                        $(this).addClass('is-selected');
                    });
                }
            }
        },
        stickyButtonAddToCartToggle: function () {
            var qtySticky = $('.js_sticky_qty');
	        var groupQty = $('.grouped .qty input[type="number"]');
	        var qty = $('#qty');
            var buttonSticky = $('.sticky_atc_js');
            var buttonAddToCart = $('#product-addtocart-button');
            var buttonBundle = $('#bundle-slide');

	        qty.on('change', function(){
	        	qtySticky.val(this.value);
	        });

	        qtySticky.on('change', function(){
	        	if(groupQty.length){
	        		groupQty.val(this.value);
	        	}
	        	qty.val(this.value);
	        });

	        groupQty.on('change', function(){
	        	qtySticky.val(this.value);
	        });

            buttonSticky.on("click", function(){
	        	var $this = $(this);
	        	$this.text(buttonAddToCart.text());
	        	$this.attr("disabled", "disabled");
	        	setTimeout(function() {
	        		$this.removeAttr("disabled");
	          	}, 1500);
	          	if($this.hasClass('customize')){
	          		buttonBundle.click();
	          		buttonSticky.removeClass('customize');
	          	}else {
	            	buttonAddToCart.click();
	          	}
	        });

            $('.sticky-addcart .quantity').each(function() {
				var spinner = $(this),
				input = spinner.find('input[type="number"]'),
				btnUp = spinner.find('.plus'),
				btnDown = spinner.find('.minus'),
				min = input.attr('min'),
				max = input.attr('max');
	          	btnUp.click(function() {
                    var oldValue = parseFloat(input.val());
                    if (oldValue >= max) {
                    var newVal = oldValue;
                    } else {
                    var newVal = oldValue + 1;
                    }
                    spinner.find("input").val(newVal);
                    spinner.find("input").trigger("change");
                    return false;
                });

                btnDown.click(function() {
                    var oldValue = parseFloat(input.val());
                    if (oldValue <= min) {
                    var newVal = oldValue;
                    } else {
                    var newVal = oldValue - 1;
                    }
                    spinner.find("input").val(newVal);
                    spinner.find("input").trigger("change");
                    return false;
                });
	        });
        },
        productImages: function () {
            var $productGallery = $('.gallery-images');
            var $self = this;
            var p_thumb = $('.p-thumb'),
                p_infors = $('.product-infors'),
                zoom_target = $('.p-thumb .gallery-img'),
                dt_zoom_img = $('.dt_img_zoom')[0],
                zoom_tp = this.options.zoomType,
                PhotoSwipeTrigger = '.show_btn_pr_gallery',
                z_magnify = 2;

            $(document).on('click', PhotoSwipeTrigger, function (e) {
                var galleryType = $productGallery.attr('data-gallery-type');
                if (galleryType == 'mfp') {
                    $.magnificPopup.open({
                        type: 'image',
                        tClose: false,
                        image: {
                            verticalFit: false
                        },
                        items: $self.getGalleryItemImages(),
                        gallery: {
                            enabled: true,
                            navigateByImgClick: false
                        },
                    }, 0);
                } else if (galleryType == 'pswp') {
                    var items = $self.getGalleryItemImages(),index = 0;
                    $self.callPhotoSwipe(index, items);
                }
            });
            if (!$productGallery.hasClass('img_action_zoom') || $(window).width() < 1025 ) return;
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
        },
        callPhotoSwipe: function (index, items) {
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
        },
        getGalleryItemImages:function () {
            var items = [],$productGallery = $('.gallery-images'),
                gallery = $productGallery.find('.gallery-img');
                gallery.each(function () {
                if (!$(this).hasClass('gallery-video')) {
                    items.push({
                        src: $(this).attr('data-bgset'),
                        w: $(this).attr('data-width'),
                        h: $(this).attr('data-height')
                    });
                }
            });
            return items;
        },
        productTabDetailed: function () {
            if ($('.product.info.detailed').hasClass('tab-accordions')) {
                $('body').on('click', '.tab-accordions .clicked_accordion .data.title a', function() {
                    if ($(this).closest('.clicked_accordion').hasClass('active')) {
                        $(this).closest('.clicked_accordion').removeClass('active');
                        $(this).closest('.clicked_accordion').find('.data.content').slideUp(300);
                    } else {
                        $(this).closest('.clicked_accordion').addClass('active');
                        $(this).closest('.clicked_accordion').find('.data.content').slideDown(300);
                    }
                    return false;
                });
            }
        },
        productInStockNotification: function () {
            $('body').on("click",'.frm_notify .notify_button', function(){
                if ($('#contact_form_notify').valid()) {
                    var formData = new FormData(),
                        notify_email = $('input[name="notify_email"]').val(),
                        url = $(this).closest('.frm_notify').attr('action'),
                        formKey = $('input[name="form_key"]').val(),
                        product_id = $('input[name="product_id"]').val();
                        formData.append('notify_email', notify_email);
                        formData.append('product_id', product_id);
                        formData.append('form_key', formKey);
                    if(notify_email){
                        $.ajax({
                            url: url, 
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            showLoader: true,
                            success:function(res){
                                $('input[name="notify_email"]').val('');
                            } 
                        });
                    }
                }
			});
        }
    });
    return $.mage.customLayout;
});