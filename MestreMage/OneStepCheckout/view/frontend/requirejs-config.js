var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/place-order': {
                'MestreMage_OneStepCheckout/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/model/quote': {
                'MestreMage_OneStepCheckout/js/model/quote-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/action/select-payment-method':
                'MestreMage_OneStepCheckout/js/action/select-payment-method'
        }
    }
};