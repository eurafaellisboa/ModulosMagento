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
                type: 'itaushopline',
                component: 'MestreMage_ItauShopline/js/view/payment/method-renderer/itaushopline-method'
            }
        );
        return Component.extend({});
    }
);