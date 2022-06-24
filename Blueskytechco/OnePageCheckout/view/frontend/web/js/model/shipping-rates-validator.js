define([
    'jquery',
    'ko',
    'underscore',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/postcode-validator',
    'Blueskytechco_OnePageCheckout/js/model/default-validator',
    'mage/translate',
    'uiRegistry',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rates-validator'
], function (
    $,
    ko,
    _,
    shippingRatesValidationRules,
    addressConverter,
    selectShippingAddress,
    postcodeValidator,
    defaultValidator,
    $t,
    uiRegistry,
    formPopUpState,
    quote,
    shippingRatesValidator
) {
    'use strict';

    var validators = [],
        postcodeElement = null,
        postcodeElementName = 'postcode';

    validators.push(defaultValidator);

    shippingRatesValidator.doElementBinding = function (element, force, delay) {
        var observableFields = shippingRatesValidationRules.getObservableFields();
        if (_.isUndefined(delay)) {
            if (element.index === 'country_id' || element.index === 'region_id') {
                delay = 0;
            } else {
                delay = 700;
            }
        }
        if (element && (observableFields.indexOf(element.index) !== -1 || force)) {
            if (element.index !== postcodeElementName) {
                this.bindHandler(element, delay);
            }
        }

        if (element.index === postcodeElementName) {
            this.bindHandler(element, delay);
            postcodeElement = element;
        }
    };

    return shippingRatesValidator;
});
