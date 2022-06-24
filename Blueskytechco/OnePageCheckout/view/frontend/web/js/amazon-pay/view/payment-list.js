define([
    'jquery',
    'underscore'
], function (
    $,
    _
) {
    'use strict';

    var mixin = {
        /**
         * @param {Object} group
         * @returns {Boolean}
         */
        showFormShared: function (group) {
            var isShow = false,
                self = this;
            if (self.paymentGroupsList().length) {
                if (_.first(self.paymentGroupsList()).alias == group().alias) {
                    isShow = true;
                }
            }
            return isShow;
        },
    };

    return function (target) {
        return target.extend(mixin);
    };
});
