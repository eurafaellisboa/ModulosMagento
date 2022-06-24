define([
    'jquery',
    'underscore',
    'mage/utils/wrapper',
    'Blueskytechco_OnePageCheckout/js/model/additional-data',
    'Blueskytechco_OnePageCheckout/js/model/agreements-assigner'
], function ($, _, wrapper, additionalData, agreementsAssigner) {
    'use strict';

    return function (placeOrderAction) {

        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            if (!_.isUndefined(window.checkoutConfig.OnePageCheckout)) {
                additionalData(paymentData);
            }
            agreementsAssigner(paymentData);
            return originalAction(paymentData, messageContainer);
        });
    };
});
