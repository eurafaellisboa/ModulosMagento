define([
    'ko',
    'Magento_Checkout/js/model/quote'
], function (ko, quote) {
    'use strict';

    var totals = quote.getTotals(),
        couponCode = ko.observable(null);

    if (totals()) {
        couponCode(totals()['coupon_code']);
    }

    return {
        isLoading: ko.observable(false),
        isAppliedCoupon: ko.observable(couponCode() != null)
    }
});
