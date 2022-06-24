define([
    'jquery'
], function ($) {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {};
        if ($('#shipping #customer_shipping_date').length || $('#opc-shipping_method #customer_shipping_date').length) {
            payload.addressInformation['extension_attributes']['customer_shipping_date'] = $('#customer_shipping_date').val();
        }
        if ($('#shipping #customer_shipping_comments').length || $('#opc-shipping_method #customer_shipping_comments').length) {
            payload.addressInformation['extension_attributes']['customer_shipping_comments'] = $('#customer_shipping_comments').val();
        }
        if ($('#shipping #delivery_time_slot').length || $('#opc-shipping_method #delivery_time_slot').length) {
            payload.addressInformation['extension_attributes']['delivery_time_slot'] = $('#delivery_time_slot').val();
        }
        return payload;
    };
});
