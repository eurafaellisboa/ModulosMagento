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

        if(window.checkoutConfig.payment.mestremagegn.homologation_mode_enabled) {
            rendererList.push(
                {
                    type: 'mestremagegn',
                    component: 'MestreMage_Getnet/js/view/payment/method-renderer/mestremagegn'
                }
            );
        }

        return Component.extend({});
    }
);  