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
                type: 'boletoitau',
                component: 'Magento_BoletoItau/js/view/payment/method-renderer/boletoitau-method'
            }
        );
        return Component.extend({});
    }
);