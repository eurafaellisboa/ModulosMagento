define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'pagarmebl',
                component: 'MestreMage_PagarMe/js/view/payment/method-renderer/pagarmebl'
            }
        );
        return Component.extend({});
    }
);