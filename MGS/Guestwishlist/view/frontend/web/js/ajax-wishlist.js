define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/translate',
    'jquery/ui',
], function ($, customerData) {
    'use strict';
	
	var guestOptions = window.guestWishlist;
	
    $.widget('mgs.ajaxWishlist', {
        options: {
            ajaxWishlist: {
                wishlistBtnSelector: '[data-action="add-to-wishlist"]',
                wishlistFormSelector: '#wishlist-view-form',
                wishlistList: '#guest_wishlist_list',
                wishlistRemoveSelector: '[data-action="remove-from-wishlist"]',
				formKeyInputSelector: 'input[name="form_key"]'
            }
        },
		_create: function() {
            this._bind();
        },
        _bind: function () {
			this.initEvents();
        },
        
        initEvents: function () {
            var self = this;
			var formKey = $(self.options.ajaxWishlist.formKeyInputSelector).val();
            
            $(document).on('click', self.options.ajaxWishlist.wishlistBtnSelector, function (e) {
                e.preventDefault();
                e.stopPropagation();
				var $_focusToWishlist = $(e.currentTarget);
				var params = $_focusToWishlist.data('post').data;
				if($_focusToWishlist.parents('.product-top').length){
					$_focusToWishlist.parents('.product-top').addClass('loading-ajax--wl');
				}
				
				params['form_key'] = formKey;
				
				var form_data = false;
				var product_form = $("#product_addtocart_form");
				var product_form_input = $("#product_addtocart_form :input[type!=hidden]");
				
				if($_focusToWishlist.parents('.product-item-info').find(".actions-primary form").length){
					product_form = $_focusToWishlist.parents('.product-item-info').find(".actions-primary form");
					product_form_input = $_focusToWishlist.parents('.product-item-info').find(".actions-primary form :input[type!=hidden]");
				}
				
				if(product_form.length){
					form_data = product_form_input.filter(function(index, element) { return $(element).val() != ""; }).serialize();
					if(form_data){
						params["buyRequest"] = form_data;
					}
				}
				
				var action = $_focusToWishlist.data('post').action.replace("wishlist", "guestwishlist");
				action += 'uenc/' + params.uenc;
				
				var customer = customerData.get('customer');
                    
				if (!customer().firstname) {
					params['guest'] = 1;
				}else {
					params['guest'] = 0;
				}
				
				$.ajax({
					url: action,
					data: params,
					method: "POST",
					context: document.body
				}).done((function(data) {
					if($_focusToWishlist.parents('.product-top').length){
						$_focusToWishlist.parents('.product-top').removeClass('loading-ajax--wl');
					}
				}).bind(this));
            });
			
			$(document).on('submit', self.options.ajaxWishlist.wishlistFormSelector, function (e) {
				var form = $(this);
				var url = form.attr('action');
				var formData = form.serialize();
				
				$.ajax({
					type: "POST",
					url: guestOptions.updateUrl,
					data: formData + "&is_ajax=1&form_key=" + formKey,
					success: function(data) { 
						$(self.options.ajaxWishlist.wishlistList).replaceWith(data.content);
					}
				});
				
				e.preventDefault();
			});
			
			$(document).on('click', self.options.ajaxWishlist.wishlistRemoveSelector, function (e) {
				e.preventDefault();
				
				var $_focusRemoveWishlist = $(e.currentTarget);
				var params = $_focusRemoveWishlist.data('url').data;
				var url = $_focusRemoveWishlist.data('url').action;
				
				$.ajax({
					type: "POST",
					url: url,
					data: {
						'itemId': params.itemId, 
						"productName" : params.productName,
						"uenc" : params.uenc,
						"is_ajax" : 1
					},
					success: function(data) {
						$(self.options.ajaxWishlist.wishlistList).replaceWith(data.content);
					}
				});
				
			});
        }
    });

    return $.mgs.ajaxWishlist;
});