define([
    'jquery',
    'ko',
    'Magento_Customer/js/customer-data'
], function ($, ko, customerData) {
    'use strict';
    
    var guestOptions = window.guestWishlist;
    
    var mixin = {
        initialize: function () {
            this._super();
            
            if (guestOptions.isActive) {
                this.wishlist = customerData.get('guest_wishlist');
            } else {
                this.wishlist = customerData.get('wishlist');
            } 
        }
    };

    return function (target) { // target == Result that Magento_Wishlist/js/view/wishlist returns.
        return target.extend(mixin); // new result that all other modules receive 
    };
});
