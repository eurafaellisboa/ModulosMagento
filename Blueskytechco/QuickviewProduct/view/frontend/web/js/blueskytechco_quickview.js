define([
    'jquery',
	'mage/translate',
    'Magento_Customer/js/customer-data',
    'Blueskytechco_QuickviewProduct/js/model/jquery.magnific-popup.min',
    'Blueskytechco_AjaxSuite/js/model/canvas-confetti',
], function ($, $t, customerData, magnificPopup) {
    'use strict';

    $.widget('mage.quickView', {
        options: {
            quickviewUrl: '',
            buttonText: '',
            actionInsert: 'append',
            classInsertPosition: '[data-role=add-to-links]',
            productItemInfo: '.product-item-info',
            classGallery: 'horizontal_bottom'
        },
        _create: function () {
            this.renderQuickViewIcon();
            this._EventListener();
            this.addClassQuickView();
            var parentBody = window.parent.document.body;
            $('.mfp-preloader', parentBody).css('display', 'none');
            $('.mfp-close', parentBody).css('display', 'block');
            $('.mfp-iframe-holder .mfp-content', parentBody).css('width', '100%');
            $('.mfp-iframe-scaler iframe', parentBody).animate({'opacity': 1}, 2000);
        },
        addClassQuickView: function () { 
            var self = this,
                classGallery = self.options.classGallery;
            if($('body').hasClass('blueskytechco_quickview-product-view')){
                $('[data-gallery-role=gallery-placeholder]').addClass(classGallery);
                $('body').addClass(classGallery);
            }
        },
        renderQuickViewIcon: function () {
            var self = this,
                classInsertPosition = self.options.classInsertPosition,
                productItemInfo = self.options.productItemInfo,
                quickviewUrl = self.options.quickviewUrl,
                buttonText = self.options.buttonText,
                actionInsert = self.options.actionInsert;
            $(classInsertPosition).each(function(){
                var id_product = false;
                var price_box = false;
                if ( $(this).find('.link-quickview').length > 0 ) return;
                if ($(this).closest(productItemInfo).find('.blueskytechco-quickview-id').length) {
                    id_product = $(this).closest(productItemInfo).find('.blueskytechco-quickview-id').data('id');
                    $(this).closest(productItemInfo).find('.blueskytechco-quickview-id').remove();
                }
                if (!id_product && $(this).closest(productItemInfo).find('.actions-primary input[name="product"]').val() !='') {
                    id_product = $(this).closest(productItemInfo).find('.actions-primary input[name="product"]').val();
                }
                if (!id_product) {
                    id_product = $(this).closest(productItemInfo).find('.price-box').data('product-id');
                }
                if (!id_product) {
                    price_box = $(this).closest(productItemInfo).find('.price-box').data('price-box');
                    if(price_box){
                        id_product = price_box.replace('product-id-', "");    
                    }
                }
                var html = '<div id="quickview-'+ id_product +'" class="quickview button_quickview">' +
                    '<a class="action link-quickview" data-product-id="' + id_product + '"' +
                    'data-quickview-url="'+quickviewUrl+'id/'+ id_product +'" href="javascript:void(0);" >' +
                    '<span>'+buttonText+'</span></a></div>';
                if(id_product && actionInsert == 'append')
                {
                    $(this).append(html);
                }else if(id_product && actionInsert == 'after'){
                    $(this).after(html);
                }else if(id_product && actionInsert == 'before'){
                    $(this).before(html);
                }
            })
        },
        _EventListener: function () {
            var self = this;
            $(document).on('click','.link-quickview', function() {
                window.showMiniCart = false;
                window.shippingFreeCanvas = false;
                var prodUrl = $(this).attr('data-quickview-url');
                if (prodUrl.length) {
                    $.magnificPopup.open({
                        items: {
                          src: prodUrl
                        },
                        type: 'iframe',
                        removalDelay: 500,
                        closeOnBgClick: true,
                        preloader: true,
                        tLoading: '',
                        mainClass: 'mfp-zoom-in',
                        callbacks: {
                            open: function() {
                                $('.mfp-preloader').css('display', 'block');
                                $('.mfp-close').css('display', 'none');
                            },
                            beforeClose: function() {
                                self.reloadCustomerData(['cart']);
                            },
                            close: function() {
                                $('.mfp-preloader').css('display', 'none');
                                $('.mfp-close').css('display', 'block');
                            },
                            afterClose: function() {
                                console.log(window.showMiniCart);
                                if (window.showMiniCart) {
                                    $('.action.showcart').trigger('click');
                                    if (window.shippingFreeCanvas && !$('body').hasClass('shipping_free_canvas')) {
                                        $('body').addClass('shipping_free_canvas');
                                        $.confetti.restart();
                                        setTimeout(function(){ $.confetti.stop() }, 3500);
                                    }
                                }
                            }
                        }
                    });
                }
            });
        },
        reloadCustomerData: function(sessionName)
        {
            customerData.reload(sessionName, false);
        }
    });

    return $.mage.quickView;
});
