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

        if(window.checkoutConfig.payment.mestremagecc.homologation_mode_enabled) {
            rendererList.push(
                {
                    type: 'mestremagecc',
                    component: 'MestreMage_Cielo/js/view/payment/method-renderer/mestremagecc'
                }
            );
        }

        return Component.extend({});
    }
);  