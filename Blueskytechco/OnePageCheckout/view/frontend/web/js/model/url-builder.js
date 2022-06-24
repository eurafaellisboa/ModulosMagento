define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'
], function ($, _, urlBuilder, customer, quote) {
    'use strict';

    var opcStoreCode;
    if (!_.isUndefined(window.checkoutConfig.OnePageCheckout.giftOptionsConfig)) {
        opcStoreCode = window.checkoutConfig.OnePageCheckout.giftOptionsConfig.storeCode;
    } else {
        opcStoreCode = window.checkoutConfig.storeCode;
    }

    return $.extend(urlBuilder, {
        storeCode: opcStoreCode,

        /**
         * Get update item url for service.
         *
         * @return {String}
         */
        getUpdateQtyUrl: function () {
            var serviceUrl;
            if (!customer.isLoggedIn()) {
                serviceUrl = this.createUrl('/blueskytechco-opc/guest-carts/:cartId/update-item-qty', {
                    cartId: quote.getQuoteId()
                });
            } else {
                serviceUrl = this.createUrl('/blueskytechco-opc/carts/mine/update-item-qty', {});
            }
            return serviceUrl;
        }
    });
});
