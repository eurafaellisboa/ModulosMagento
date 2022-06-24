define([
    'jquery',
    'Magento_Checkout/js/view/summary/item/details',
    'mage/translate',
    'ko',
    'underscore',
    'Magento_Customer/js/customer-data',
    'Blueskytechco_OnePageCheckout/js/action/update-item',
    'Magento_Checkout/js/model/quote'
], function ($, Component, $t, ko, _, customerData, updateItemAction, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Blueskytechco_OnePageCheckout/summary/item/details'
        },

        titleQtyBox: ko.observable($t('Qty')),
        number: null,

        /**
         * @param {Object} item
         * @returns void
         */
        updateQty: function (item) {
            if (item.qty < 0) {
                $(".error-message[itemId = '" + item.item_id + "']").text($t('Please enter the number greater than or equal to 0!'));
                return;
            }
            if (parseFloat(item.qty) != item.qty) {
                $(".error-message[itemId = '" + item.item_id + "']").text($t('Please enter number!'));
                return;
            }
            $(".error-message[itemId = '" + item.item_id + "']").text($t(''));
            updateItemAction(item).done(
                function (response) {
                    var totals = response.totals,
                        data = JSON.parse(this.data),
                        itemId = data.itemId,
                        itemsOrigin = [],
                        quoteItemData = window.checkoutConfig.quoteItemData;
                    if (!response.status) {
                        var originItem = _.find(quoteItemData, function (index) {
                            return index.item_id == itemId;
                        });
                        $.each(totals.items, function (index) {
                            if (this.item_id == originItem.item_id) {
                                this.qty = originItem.qty;
                            }
                            itemsOrigin[index] = this;
                        });
                        totals.items = itemsOrigin;
                    } else {
                        customerData.reload('cart');
                    }
                    quote.setTotals(totals);
                }
            );
        },

        /**
         * @param data
         * @param event
         */
        updateQtyButton: function (data, event) {
            var element = event.target,
                action = element.getAttribute('action'),
                itemId = element.getAttribute('itemId'),
                qtyInput = $('[itemId = ' + itemId + ']').parent().parent().find('input');
            if (typeof action === "undefined" || typeof itemId === "undefined" || typeof qtyInput === "undefined") {
                return;
            }
            var currentQty = parseFloat(qtyInput.val());
            currentQty = Math.round(currentQty * 100);
            if (this.number != null && currentQty >= 100) {
                clearTimeout(this.number);
            }
            if (action == 'increase') {
                var nextQty = (currentQty + 100)/100;
                nextQty = +nextQty.toFixed(2);
                qtyInput.val(nextQty);
                this.number = setTimeout(function () {
                    qtyInput.trigger('change');
                }, 1000);
            } else {
                if (currentQty >= 100) {
                    var nextQty = (currentQty - 100)/100;
                    nextQty = +nextQty.toFixed(2);
                    qtyInput.val(nextQty);
                    this.number = setTimeout(function () {
                        qtyInput.trigger('change');
                    }, 1000);
                }
            }
        },

        /**
         * @param {*} itemId
         * @returns {String}
         */
        getProductUrl: function (itemId) {
            if (_.isUndefined(customerData.get('cart')())) {
                customerData.reload('cart');
            }
            var productUrl = 'javascript:void(0)',
                cartData = customerData.get('cart')(),
                items = cartData.items;

            var item = _.find(items, function (item) {
                return item.item_id == itemId;
            });

            if (!_.isUndefined(item) && item.product_has_url) {
                productUrl = item.product_url;
            }
            return productUrl;
        }
    });
});
