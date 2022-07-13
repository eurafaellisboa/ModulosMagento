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

        if(window.checkoutConfig.payment.mestremagedc.homologation_mode_enabled) {
            rendererList.push(
                {
                    type: 'mestremagedc',
                    component: 'MestreMage_Cielo/js/view/payment/method-renderer/mestremagedc'
                }
            );
        }

        return Component.extend({});
    }
);