define([
    'jquery',
    'Magento_Customer/js/model/customer',
    'Blueskytechco_OnePageCheckout/js/model/save-new-account-information',
    'mage/validation'
], function ($, customer, saveNewAccountInformation) {
    'use strict';

    return {
        /**
         * Validate checkout agreements
         *
         * @returns {Boolean}
         */
        validate: function () {
            var validationResult = true,
                createNewAccountCheckBoxId = 'create-new-customer',
                loginFormSelector = 'form[data-role=email-with-possible-login]';

            if (!customer.isLoggedIn() && $(loginFormSelector + ' #'+createNewAccountCheckBoxId).is(":checked")) {
                $(loginFormSelector).validation();
                validationResult = Boolean($(loginFormSelector + ' input[name=newcustomerpassword]').valid());
                if (validationResult == true) {
                    validationResult = Boolean($(loginFormSelector + ' input[name=newcustomerpassword_confirmation]').valid());
                }
            }
            saveNewAccountInformation.ajaxSave();
            return validationResult;
        }
    };
});
