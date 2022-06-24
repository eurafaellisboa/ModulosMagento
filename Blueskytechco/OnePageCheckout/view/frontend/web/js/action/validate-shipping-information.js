define([
    'Blueskytechco_OnePageCheckout/js/model/shipping-save-processor/validate'
], function (validateProcessor) {
    'use strict';

    return function () {
        return validateProcessor.saveShippingInformation();
    };
});
