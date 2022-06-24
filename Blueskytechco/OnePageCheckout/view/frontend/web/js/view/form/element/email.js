define([
    'jquery',
    'Magento_Checkout/js/view/form/element/email',
    'mage/validation'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Blueskytechco_OnePageCheckout/form/element/email',
            createNewAccount: false

        },
        checkDelay: 1000,
        initObservable: function () {
            this._super()
                .observe(['email', 'emailFocused', 'isLoading', 'isPasswordVisible', 'createNewAccount']);
            return this;
        },
        createNewAccountConfig: function () {
            return window.checkoutConfig.OnePageCheckout.autoCreateNewAccount.enable;
        },
        createNewAccountChecked: function (element) {
            if ($('#' + element).is(":checked")) {
                this.createNewAccount(true);
            } else {
                this.createNewAccount(false);
            }
        },
        minLength: function () {
            return window.checkoutConfig.OnePageCheckout.autoCreateNewAccount.minLength;
        },
        minCharacterSets: function () {
            return window.checkoutConfig.OnePageCheckout.autoCreateNewAccount.minCharacterSets;
        }
    });
});
