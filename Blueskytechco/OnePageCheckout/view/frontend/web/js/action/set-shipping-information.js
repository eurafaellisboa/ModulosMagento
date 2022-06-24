define([
    'Magento_Checkout/js/model/quote',
    'Blueskytechco_OnePageCheckout/js/model/shipping-save-processor'
], function (quote, shippingSaveProcessor) {
    'use strict';

    return function () {
        return shippingSaveProcessor.saveShippingInformation(quote.shippingAddress().getType());
    };
});
