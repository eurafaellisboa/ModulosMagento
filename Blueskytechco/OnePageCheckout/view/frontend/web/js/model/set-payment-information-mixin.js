define([
    'jquery',
    'mage/utils/wrapper',
    'Blueskytechco_OnePageCheckout/js/model/additional-data',
    'Blueskytechco_OnePageCheckout/js/model/agreements-assigner',
    'underscore',
    'Magento_Checkout/js/checkout-data',
], function ($, wrapper, additionalData, agreementsAssigner, _, checkoutData) {
    'use strict';

    return function (placeOrderAction) {

        /** Override place-order-mixin for set-payment-information action as they differs only by method signature */
        return wrapper.wrap(placeOrderAction, function (originalAction, messageContainer, paymentData) {
            if (!_.isUndefined(window.checkoutConfig.OnePageCheckout)) {
                additionalData(paymentData);
            }
            agreementsAssigner(paymentData);

            // only send request when email is filled
            if (window.checkoutConfig.isCustomerLoggedIn &&
			!_.isEmpty(checkoutData.getSelectedShippingAddress())) {
                return originalAction(messageContainer, paymentData);
            }
            if (!_.isEmpty(checkoutData.getValidatedEmailValue())) {
                return originalAction(messageContainer, paymentData);
            }
            return originalAction(messageContainer, paymentData);
        });
    };
});
