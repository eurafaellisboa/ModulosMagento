define([
    'Magento_Paypal/js/in-context/express-checkout-smart-buttons'
], function (checkoutSmartButtons) {
    'use strict';
    window.paypalElement = false;
    return function(target){
        target.renderPayPalButtons = function (element) {
            if (window.paypalElement == false) {
                window.paypalElement = element;
            }
            if (typeof window.checkoutConfig === "undefined") {
                checkoutSmartButtons(this.prepareClientConfig(), element);
            }
        }
        return target;
    };
});
