define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/full-screen-loader',
        'jquery',
        'Magento_Checkout/js/action/get-totals',
    ],
    function (quote, fullScreenLoader, jQuery, getTotalsAction) {
        'use strict';
        return function (paymentMethod) {
            quote.paymentMethod(paymentMethod);

            fullScreenLoader.startLoader();

            jQuery.ajax(BASE_URL+'mestremage_onestepcheckout/checkout/applyPaymentMethod', {
                data: {payment_method: paymentMethod},
                complete: function () {
                    getTotalsAction([]);
                    fullScreenLoader.stopLoader();
                }
            });

        }
    }
);
