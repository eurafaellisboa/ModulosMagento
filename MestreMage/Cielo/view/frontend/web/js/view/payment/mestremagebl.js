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
                type: 'mestremagebl',
                component: 'MestreMage_Cielo/js/view/payment/method-renderer/mestremagebl'
            }
        );
        return Component.extend({});
    }
);