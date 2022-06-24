define(
    [
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager'
    ],
    function ($, resourceUrlManager) {
        "use strict";

        return $.extend({
            /** Get url for update item qty and remove item */
            getUrlForUpdatePaymentTotalInformation: function (quote) {
                var params = this.getCheckoutMethod() === 'guest' ? {cartId: quote.getQuoteId()} : {};
                var urls   = {
                    'guest': '/guest-carts/:cartId/payment-total-information',
                    'customer': '/carts/mine/payment-total-information'
                };

                return this.getUrl(urls, params);
            }
        }, resourceUrlManager);
    }
);
