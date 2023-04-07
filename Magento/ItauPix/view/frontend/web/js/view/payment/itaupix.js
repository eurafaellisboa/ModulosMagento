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
                type: 'itaupix',
                component: 'Magento_ItauPix/js/view/payment/method-renderer/itaupix'
            }
        );
        return Component.extend({});
    }
);