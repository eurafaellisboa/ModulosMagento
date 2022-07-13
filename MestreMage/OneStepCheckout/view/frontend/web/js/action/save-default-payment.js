define(
    [
        'ko',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/method-converter'
    ],
    function (
        ko,
        paymentService,
        quote,
        selectPaymentMethodAction,
        checkoutData
    ) {
        'use strict';
        return function () {
            if(paymentService.getAvailablePaymentMethods().length == 1){
                var methods = paymentService.getAvailablePaymentMethods();
                var method = methods.pop();
                if(quote.paymentMethod()){
                    method = quote.paymentMethod();
                }
                if(method && !quote.paymentMethod()){
                    selectPaymentMethodAction(method);
                    checkoutData.setSelectedPaymentMethod(method.method);
                }
            }
        };
    }
);