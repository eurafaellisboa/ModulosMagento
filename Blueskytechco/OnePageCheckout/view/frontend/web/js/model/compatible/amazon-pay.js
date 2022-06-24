define(['ko'], function (ko) {
    'use strict';
    var hasLogin = window.checkoutConfig.OnePageCheckout.isAmazonAccountLoggedIn;
    return {
        isAmazonAccountLoggedIn: ko.observable(hasLogin),
        setLogin: function (value) {
            return this.isAmazonAccountLoggedIn(value);
        }
    };
});
