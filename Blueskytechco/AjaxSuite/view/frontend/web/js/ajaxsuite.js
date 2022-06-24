define([
    'jquery',
    'mage/translate',
    'Magento_Customer/js/customer-data',
    'Blueskytechco_AjaxSuite/js/model/ajaxsuite-popup',
    'Blueskytechco_AjaxSuite/js/model/canvas-confetti',
    'mage/validation/validation'
], function ($, $t, customerData, ajaxsuitepopup) {
    'use strict';

    $.widget('blueskytechco.ajaxsuite', {
        options: {
                popupWrapperSelector : '#mb-ajaxsuite-popup-wrapper',
                ajaxCart: {
                    enabled: 0,
                    actionAfterSuccess: 'popup',
                    continueShoppingSelector: '#button_continue_shopping',
                    minicartSelector: '[data-block="minicart"]',
                    messagesSelector: '[data-placeholder="messages"]',
                    initConfig: {
                        'show_success_message': true,
                        'timerErrorMessage': 3000,
                        'addWishlistItemUrl': null
                    },
                    formKey: null,
                    formKeyInputSelector: 'input[name="form_key"]',
                    addToCartButtonSelector: 'button.tocart',
                    addToCartUrl: null,
                    addToCartInWishlistUrl: null,
                    wishlistAddToCartUrl: null,
                    checkoutCartUrl: null,
                    addToCartButtonDisabledClass: 'loading',
                    addToCartButtonTextWhileAdding: $t('Adding...'),
                    addToCartButtonTextAdded: $t('Added'),
                    addToCartButtonTextDefault: $t('Add to Cart')
                },
                ajaxWishList: {
                    enabled: 0,
                    WishlistUrl: null,
                    wishlistBtnSelector: '[data-action="add-to-wishlist"]',
                    btnCloseSelector: '#ajaxwishlist_btn_close_popup',
                    btnCancelSelector: '#ajaxwishlist_btn_cancel',
                    btnToLoginSelector: '#ajaxwishlist_btn_to_login'
                },
                ajaxCompare: {
                    enabled: 0,
                    compareSelector: '.tocompare',
                    CompareUrl: null,
                },
                quickView: {
                    enabled: 0
                },
                popupSelector: '#ajaxsuite-popup-content',
                cartBottom: '.link-cart-bottom',
                loginUrl: null,
                customerId: null

        },
        _create: function() {
            this._bind();
            this.options.popupWrapper = $('<div />', {
                    'id': 'mb-ajaxsuite-popup-wrapper'
                }).appendTo($('#ajaxsuite-popup-content'));
        },
        showModal: function (element) {
            ajaxsuitepopup.createPopUp(element);
            ajaxsuitepopup.showModal();
            return ajaxsuitepopup;
        },
        getCustomerData: function()
        {
            var customer = customerData.get('customer');
            var customerInfo = customer();
            if (customerInfo && customerInfo.fullname) {
                return true;
            }
            return false;
        },
        initEventsWishlist: function()
        {
            var self = this;
            var get_customer_data = this.getCustomerData();
            
            if(!get_customer_data){
                //$(self.options.ajaxWishList.wishlistBtnSelector).addClass("trigger-auth-popup").attr('data-action', 'ajax-popup-login').removeAttr("data-post");
            }
            $('body').on('click',self.options.ajaxWishList.wishlistBtnSelector, function (e) {
                if (!get_customer_data) {
                    $(self.options.ajaxWishList.wishlistBtnSelector).addClass("trigger-auth-popup").attr('data-action', 'ajax-popup-login').attr('href', 'javascript:void(0);').removeAttr("data-post");
                    e.preventDefault();
                    return;
                }
                var _this_fixed = $(this);
                _this_fixed.addClass('loading');
                e.preventDefault();
                e.stopPropagation();
                if($(this).data('post'))
                {
                    var params = $(this).data('post').data;
                }else
                {
                    var params = {};
                }
                params['ajax_post'] = true;
                $('body').trigger('processStart');
                $.ajax({
                    url: self.options.ajaxWishList.WishlistUrl,
                    data: params,
                    type: 'post',
                    showLoader: false,
                    dataType: 'json',
                    success: function (res) {
                        ajaxsuitepopup.hideModal();
                        if (res.html_popup) {
                            self.options.popupWrapper.html(res.html_popup);
                            self.showModal(self.options.popupWrapper);
                        }
                        self.reloadCustomerData(['wishlist']);
                        _this_fixed.removeClass('loading');
                    },
                    error: function (res) {
                        alert('Error in sending ajax request');
                        _this_fixed.removeClass('loading');
                    }
                });
                $('body').trigger('processStop');
            });
        },
        initEventsCompare: function () {
            var self = this;
            $('body').on('click',self.options.ajaxCompare.compareSelector, function (e) {

                e.preventDefault();
                e.stopPropagation();
                var _this_fixed = $(this);
                _this_fixed.addClass('loading');
                var params = $(this).data('post').data;
                if($(this).data('post'))
                {
                    var params = $(this).data('post').data;
                }else
                {
                    var params = {};
                }
                $('body').trigger('processStart');
                $.ajax({
                    url: self.options.ajaxCompare.CompareUrl,
                    data: params,
                    type: 'post',
                    showLoader: false,
                    dataType: 'json',
                    success: function (res) {
                        ajaxsuitepopup.hideModal();
                        if (res.html_popup) {
                            self.options.popupWrapper.html(res.html_popup);
                            self.showModal(self.options.popupWrapper);
                        }
                        self.reloadCustomerData(['compare-products']);
                        _this_fixed.removeClass('loading');
                    },
                    error: function (res) {
                        alert('Error in sending ajax request');
                        _this_fixed.removeClass('loading');
                    }
                });
                $('body').trigger('processStop');
            });
        },
        initEventsAjaxCart: function()
        {
            var self = this;
            $('body').delegate(self.options.ajaxCart.addToCartButtonSelector, 'click', function (e) {
                var form = $(this).closest('form');
                if(form.length)
                {
                    var action = form.attr('action');
                    if(action.indexOf('checkout/cart/add') != -1)
                    {
                        e.preventDefault();
                        if ($(this).closest('.product-info-main').length) {
                            var dataForm = $(this).closest('form#product_addtocart_form');
                            var validate = dataForm.validation('isValid');
                            if (validate) {
                                var form = $(this).closest('form');
                                self.ajaxCartSubmit(form);
                            }
                            return;
                        }
                        else if($(this).closest('.product-item-info').length) {
                            var have_options = $(this).closest('.product-item-info').find('.swatch-attribute').length;
                            if(have_options){
                                var selected_options = $(this).closest('.product-item-info').find('.swatch-option.selected').length;
                                if(selected_options == have_options){
                                    $(this).closest('.product-item-info').removeClass('error_validation--product-options');
                                    $(this).closest('.product-item-info').find('.msg_validation--product-options').remove();
                                    self.ajaxCartSubmit(form);
                                }
                                else{
                                    $(this).closest('.product-item-info').find('.product_item_images').append('<div class="msg_validation--product-options message-error error message"><div class="msg_validation--product-options_text">'+$t('Please select product options.')+'</div></div>');
                                    $(this).closest('.product-item-info').addClass('error_validation--product-options');
                                }
                            }
                            else{
                                self.ajaxCartSubmit(form);
                            }
                            return;
                        }
                        self.ajaxCartSubmit(form);
                    }
                }
            });
            $(document).click(function() {
                //if (!$(event.target).is("button")) { // ORIGINAL
				if (!$(event.target).is("button")) {
				
                    $('.product-item-info').removeClass('error_validation--product-options');
                    $('.product-item-info').find('.msg_validation--product-options').remove();
                }
            });
            $('body').on('click', self.options.ajaxCart.continueShoppingSelector, function (e) {
                ajaxsuitepopup.hideModal();
            });

            $('body').on('click', self.options.ajaxCart.continueShoppingSelector, function (e) {
                ajaxsuitepopup.hideModal();
            });

            $(document).on('ajaxComplete', function (event, xhr, settings) {
                var parentBody = window.parent.document.body;
                var cart = customerData.get('cart')();
                if (settings.type.match(/get/i) && _.isObject(xhr.responseJSON)) {
                    var result = xhr.responseJSON;
                    var cartMessage = false;
                    var shippingFreeCanvas = false;
                    if (_.isObject(result.messages)) {
                        var messageLength = result.messages.messages.length;
                        var message = result.messages.messages[0];
                        if (messageLength && message.type == 'success') {
                            cartMessage = message.text;
                        }
                    }
                    if (_.isObject(result.cart) && _.isObject(result.messages)) {
                        var messageLength = result.messages.messages.length;
                        var message = result.messages.messages[0];
                        if (messageLength && message.type == 'success') {
                            cartMessage = message.text;
                            if (result.cart.shipping_free_canvas) {
                                shippingFreeCanvas = true;
                            }
                        }
                    }
                    if (cartMessage) {
                        if(self.options.ajaxCart.actionAfterSuccess !== 'popup')
                        {
                            window.parent.showMiniCart = true;
                            if (shippingFreeCanvas) {
                                window.parent.shippingFreeCanvas = true;
                            }
                            $('.mfp-close', parentBody).trigger('click');
                            return
                        }
                    }
                }
                if (settings.type.match(/get/i)
                    && settings.url.match(/customer\/section\/load/i)
                    && _.isObject(xhr.responseJSON) &&
                    xhr.responseJSON.cart
                ) {
                    if($('body').hasClass('ajax_ld'))
                    {
                        $('body').removeClass('ajax_ld');
                        $('body').addClass('ajax_end');
                        $(self.options.ajaxCart.minicartSelector + ' a.showcart').trigger('click');
                        if (!$('.cart_thres_js').length) {
                            return;
                        }
                        if (!$('body').hasClass('shipping_free_canvas')){
                            if (cart.shipping_free_canvas) {
                                $('body').addClass('shipping_free_canvas');
                                $.confetti.restart();
                                setTimeout(function(){ $.confetti.stop() }, 3500);
                            }
                        }
                    }

                    if($(self.options.ajaxCart.minicartSelector).find('.showcart').hasClass('active'))
                    {
                        if (!$('.cart_thres_js').length) {
                            return;
                        }
                        if (!$('.cart_thres_js').hasClass('shipping_free')) {
                            $('.cart_thres_js').addClass('shipping_free');
                            if (cart.shipping_free_canvas) {
                                $('body').addClass('shipping_free_canvas');
                            }
                            return;
                        }
                        if (!$('body').hasClass('shipping_free_canvas')){
                            if (cart.shipping_free_canvas) {
                                $('body').addClass('shipping_free_canvas');
                                $.confetti.restart();
                                setTimeout(function(){ $.confetti.stop() }, 3500);
                            } else {
                                $('body').removeClass('shipping_free_canvas');
                            }
                        } else {
                            if (cart.shipping_free_canvas == false) {
                                $('body').removeClass('shipping_free_canvas');
                            }
                        }
                    }

                    if($('body').hasClass('checkout-cart-index'))
                    {
                        if (!$('.free-ship-calculated .cart_thres_js').length) {
                            return;
                        }
                        if (cart.shipping_free) {
                            $('.free-ship-calculated .cart_thres_js').html(cart.shipping_free);
                        } else {
                            return;
                        }
                        if ($('body').hasClass('checkout_cart_canvas')){
                            if (!$('body').hasClass('shipping_free_canvas')){
                                if (cart.shipping_free_canvas) {
                                    $('body').addClass('shipping_free_canvas');
                                    $.confetti.restart();
                                    setTimeout(function(){ $.confetti.stop() }, 3500);
                                } else {
                                    $('body').removeClass('shipping_free_canvas');
                                }
                            } else {
                                if (cart.shipping_free_canvas == false) {
                                    $('body').removeClass('shipping_free_canvas');
                                }
                            }
                        } else {
                            $('body').addClass('checkout_cart_canvas');
                            if (cart.shipping_free_canvas) {
                                $('body').addClass('shipping_free_canvas');
                            }
                        }
                    }
                }
            });
        },
        initUpdateCartButtom: function()
        {
            var self = this;
            $('body').on('click', self.options.cartBottom, function (e) {
                $(self.options.ajaxCart.minicartSelector + ' a.showcart').trigger('click');
            });

            $(document).on('ajaxComplete', function (event, xhr, settings) {
                if (settings.type.match(/get/i)
                    && settings.url.match(/customer\/section\/load/i)
                    && _.isObject(xhr.responseJSON) &&
                    xhr.responseJSON.cart
                ) {
                    var cart = customerData.get('cart')();
                    $(self.options.cartBottom).find('.count').html(cart.summary_count);
                }
            });

            $("[data-block='minicart']").on("dropdowndialogopen", (e) => {
                setTimeout(function(){
                    $('body').removeClass('ajax_end');
                }, 500);
                $('html').addClass('hside_opened');          
            });
            $("[data-block='minicart']").on("dropdowndialogclose", (e) => {
                $('html').removeClass('hside_opened');
            });
        },
        ajaxCartSubmit: function (form) {
            var self = this;
            $(self.options.ajaxCart.minicartSelector).trigger('contentLoading');
            if(self.options.ajaxCart.actionAfterSuccess !== 'popup')
            {
                $('body').addClass('ajax_ld');
            }
            self.disableAddToCartButton(form);
            $.ajax({
                url: form.attr('action').replace('checkout/cart', 'ajaxsuite/cart'),
                data: form.serialize(),
                type: 'post',
                showLoader: false,
                dataType: 'json',
                success: function (res) {
                    ajaxsuitepopup.hideModal();
                    if (res.success) {
                        if(self.options.ajaxCart.actionAfterSuccess == 'popup')
                        {
                            self.options.popupWrapper.html(res.success);
                            self.showModal(self.options.popupWrapper);
                        }else{
                            if ($('.cart_thres_3').length) {
                                $('body').addClass('shipping_free_canvas');
                            } else {
                                $('body').removeClass('shipping_free_canvas');
                            }
                        }
                        self.reloadCustomerData(['cart']);
                    }
                    else if (res.error && res.url) {
                        window.location.href = res.url;
                    }else if (res.error && res.content) {
                        if(!form.closest(self.options.popupWrapperSelector).length)
                        {
                            self.options.popupWrapper.html(res.content);
                            self.showModal(self.options.popupWrapper);
                        }
                    }else if(res.error)
                    {
                        self.options.popupWrapper.html(res.error);
                        self.showModal(self.options.popupWrapper);
                        window.location.reload();
                    }
                    self.enableAddToCartButton(form);
                    $(self.options.ajaxCart.minicartSelector).trigger('contentUpdated');
                }
            });
        },
        disableAddToCartButton: function (form) {
            var addToCartButton = $(form).find(this.options.ajaxCart.addToCartButtonSelector);
            addToCartButton.addClass(this.options.ajaxCart.addToCartButtonDisabledClass);
            addToCartButton.attr('title', this.options.ajaxCart.addToCartButtonTextWhileAdding);
            addToCartButton.find('span').text(this.options.ajaxCart.addToCartButtonTextWhileAdding);
        },
        enableAddToCartButton: function (form) {
            var self = this, addToCartButton = $(form).find(this.options.ajaxCart.addToCartButtonSelector);
            addToCartButton.find('span').text(this.options.ajaxCart.addToCartButtonTextAdded);
            addToCartButton.attr('title', this.options.ajaxCart.addToCartButtonTextAdded);

            setTimeout(function () {
                addToCartButton.removeClass(self.options.ajaxCart.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(self.options.ajaxCart.addToCartButtonTextDefault);
                addToCartButton.attr('title', self.options.ajaxCart.addToCartButtonTextDefault);
            }, 1000);
        },
        reloadCustomerData: function(sessionName)
        {
            customerData.reload(sessionName, false);
        },
        _bind: function () {
            if(this.options.ajaxCart.enabled)
            {
               this.initEventsAjaxCart();
            }
            if(this.options.ajaxWishList.enabled)
            {
                this.initEventsWishlist();
            }
            if(this.options.ajaxCompare.enabled)
            {
                this.initEventsCompare();
            }
            this.initUpdateCartButtom();
        }
    });
    return $.blueskytechco.ajaxsuite;
});
