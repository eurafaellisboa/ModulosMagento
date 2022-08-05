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

        if(window.checkoutConfig.payment.magentogn.homologation_mode_enabled) {
            rendererList.push(
                {
                    type: 'magentogn',
                    component: 'Magento_Getnet/js/view/payment/method-renderer/magentogn'
                }
            );
        }

        return Component.extend({});
    }
);  