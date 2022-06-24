define([
    'underscore'
], function (_) {
    'use strict';

    return function (stepNavigator) {
        if (!_.isUndefined(window.checkoutConfig.OnePageCheckout)) {
            stepNavigator.isProcessed = function () {
                return true;
            };
        }
        return stepNavigator;
    };
});
