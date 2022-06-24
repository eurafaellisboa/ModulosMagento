define([
    'uiComponent',
    'jquery',
    'ko',
    'Amazon_Payment/js/model/storage',
    'Magento_Checkout/js/model/shipping-rate-service',
    'Blueskytechco_OnePageCheckout/js/action/payment-total-information',
    'Blueskytechco_OnePageCheckout/js/model/compatible/amazon-pay',
    'Magento_Checkout/js/model/quote'
], function (Component, $, ko, amazonStorage, shippingService, getPaymentTotalInformation, amazonPay, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Amazon_Payment/shipping-address/inline-form',
            formSelector: 'co-shipping-form'
        },

        /**
         * Init inline form
         */
        initObservable: function () {
            this._super();
            amazonStorage.isAmazonAccountLoggedIn.subscribe(function (value) {
                if (value == false) {
                    window.checkoutConfig.OnePageCheckout.isAmazonAccountLoggedIn = value;
                    amazonPay.setLogin(value);
                    if (!quote.isVirtual()) {
                        shippingService.estimateShippingMethod();
                    }
                    getPaymentTotalInformation();
                }

                var elem = document.getElementById(this.formSelector);

                if (elem && value === false) {
                    document.getElementById(this.formSelector).style.display = 'block';
                }
            }, this);

            return this;
        },

        /**
         * Show/hide inline form
         */
        manipulateInlineForm: function () {
            if (amazonStorage.isAmazonAccountLoggedIn()) {
                window.checkoutConfig.OnePageCheckout.isAmazonAccountLoggedIn = true;
                amazonPay.setLogin(true);
                setTimeout(function () {
                    if (!quote.isVirtual()) {
                        shippingService.estimateShippingMethod();
                    }
                    getPaymentTotalInformation();
                }, 1000);

                var elem = document.getElementById(this.formSelector);

                if (elem) {
                    document.getElementById(this.formSelector).style.display = 'none';
                }
            }
        }
    });
});
