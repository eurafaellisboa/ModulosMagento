define([
    'jquery'
], function ($) {
    'use strict';

    if ($('#shipping-delivery-date').length) {
        var date = $('#shipping-delivery-date').detach();
        $('.order-shipping-method').append(date);
    }
    if ($('#shipping-delivery-comment').length) {
        var comment = $('#shipping-delivery-comment').detach();
        $('.order-shipping-method').append(comment);
    }
});
