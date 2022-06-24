define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';
    
    var guestOptions = window.guestWishlist;
    
    return function (widget) {

        $.widget('mgs.addToWishlist', widget, {
            _create: function () {
                this._super();
            },
            
            _changePostActionNotLoggedIn: function() {
                if (!guestOptions.isActive) {
                    return;
                }
                
                var customer = customerData.get('customer');
                if (!customer().firstname) {
                    $('[data-action="add-to-wishlist"]').each(function (index, element) {
                        var params = $(element).data('post');

                        if (!params) {
                            params = {
                                'data': {}
                            };
                        }

                        params.action = guestOptions.addUrl;
                        $(element).data('post', params);
                    });
                }
            }
        });

        return $.mgs.addToWishlist;
    };
});