var config = {
    map: {
        '*': {
            'Magento_Checkout/template/billing-address/form.html':
                'Blueskytechco_OnePageCheckout/template/billing-address/form.html',
            'Magento_Checkout/js/model/shipping-rate-service':
                'Blueskytechco_OnePageCheckout/js/model/shipping-rate-service',
            'Magento_Checkout/js/action/get-payment-information':
                'Blueskytechco_OnePageCheckout/js/action/get-payment-information'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Blueskytechco_OnePageCheckout/js/model/place-order-mixin': true,
                'Magento_CheckoutAgreements/js/model/place-order-mixin': false
            },
            'Magento_Checkout/js/model/step-navigator': {
                'Blueskytechco_OnePageCheckout/js/model/step-navigator-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magento_CheckoutAgreements/js/model/set-payment-information-mixin': false,
                'Blueskytechco_OnePageCheckout/js/model/set-payment-information-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validation-rules': {
                'Blueskytechco_OnePageCheckout/js/model/shipping-rates-validation-rules-mixin': true
            },
            'Magento_Paypal/js/in-context/express-checkout-wrapper': {
                'Blueskytechco_OnePageCheckout/js/paypal/in-context/express-checkout-wrapper-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Blueskytechco_OnePageCheckout/js/paypal/view/payment/method-renderer/in-context/checkout-express-mixin': true
            },
            'Amazon_Payment/js/view/payment/list': {
                'Blueskytechco_OnePageCheckout/js/amazon-pay/view/payment-list': true
            },
            'Amazon_Payment/js/view/checkout-revert': {
                'Blueskytechco_OnePageCheckout/js/amazon-pay/view/checkout-revert-rewrite': true
            }
        }
    }
};
