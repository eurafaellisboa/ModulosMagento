define([
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment/method-list',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Checkout/js/model/payment-service'
], function (_, quote, methodList, selectPaymentMethod, paymentService) {
    'use strict';

    /**
    * Free method filter
    * @param {Object} paymentMethod
    * @return {Boolean}
    */
    var isFreePaymentMethod = function (paymentMethod) {
            return paymentMethod.method === 'free';
        },

        /**
         * Grabs the grand total from quote
         * @return {Number}
         */
        getGrandTotal = function () {
            return quote.totals()['grand_total'];
        };

    return {

        /**
         * Populate the list of payment methods
         * @param {Array} methods
         */
        setPaymentMethods: function (methods) {
            var freeMethod,
                filteredMethods,
                methodIsAvailable,
                methodNames;

            freeMethod = _.find(methods, isFreePaymentMethod);
            paymentService.isFreeAvailable = !!freeMethod;

            if (freeMethod && getGrandTotal() <= 0) {
                methods.splice(0, methods.length, freeMethod);
                selectPaymentMethod(freeMethod);
            }

            filteredMethods = _.without(methods, freeMethod);

            if (filteredMethods.length === 1) {
                selectPaymentMethod(filteredMethods[0]);
            } else if (quote.paymentMethod()) {
                methodIsAvailable = methods.some(function (item) {
                    return item.method === quote.paymentMethod().method;
                });
                //Unset selected payment method if not available
                if (!methodIsAvailable) {
                    selectPaymentMethod(null);
                }
            }

            /**
             * Overwrite methods with existing methods to preserve ko array references.
             * This prevent ko from re-rendering those methods.
             */
            methodNames = _.pluck(methods, 'method');
            _.map(methodList(), function (existingMethod) {
                var existingMethodIndex = methodNames.indexOf(existingMethod.method);

                if (existingMethodIndex !== -1) {
                    methods[existingMethodIndex] = existingMethod;
                }
            });

            methodList(methods);
        }
    };
});
