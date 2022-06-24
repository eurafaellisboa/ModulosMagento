/**
 * @api
 */
define([
    'Magento_GiftMessage/js/model/gift-message'
], function (GiftMessage) {
    'use strict';

    window.giftOptionsConfig = window.checkoutConfig.OnePageCheckout.giftOptionsConfig;

    return function (itemId) {
        return new GiftMessage(itemId);
    };
});
