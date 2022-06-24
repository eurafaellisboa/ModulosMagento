define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Blueskytechco_OnePageCheckout/js/model/create-new-account-validator'
], function (Component, additionalValidators, agreementValidator) {
    'use strict';

    additionalValidators.registerValidator(agreementValidator);

    return Component.extend({});
});
