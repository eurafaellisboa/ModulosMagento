define([
    'jquery',
    'uiRegistry',
    'underscore'
], function ($, registry, _) {
    'use strict';

    return function (paymentData) {
        var additionalData = {};
        var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
        if (!_.isEmpty(shippingAddressComponent)) {
            var deliveryDate = shippingAddressComponent.getChild('before-shipping-method-form').getChild('blueskytechco_opc_shipping_delivery_date');
            var deliveryComment = shippingAddressComponent.getChild('before-shipping-method-form').getChild('blueskytechco_opc_shipping_delivery_comment');
        }
        var orderComment = registry.get('checkout.sidebar.blueskytechco_opc_order_comment');
        var subscribe = registry.get('checkout.sidebar.subscribe');

        if (!_.isUndefined(deliveryDate)) {
            additionalData['customer_shipping_date'] = deliveryDate.value();
        }
        if (!_.isUndefined(deliveryComment)) {
            additionalData['customer_shipping_comments'] = deliveryComment.value();
        }
        if (!_.isUndefined(orderComment)) {
            additionalData['order_comment'] = orderComment.value();
        }
        if (!_.isUndefined(subscribe)) {
            additionalData['subscribe'] = subscribe.value();
        }
        if (!additionalData) {
            return;
        }
        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }
        console.log(additionalData);
        paymentData['extension_attributes']['blueskytechco_opc'] = additionalData;
    };
});
