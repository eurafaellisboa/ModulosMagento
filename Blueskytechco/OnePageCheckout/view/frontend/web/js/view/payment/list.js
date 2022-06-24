define([
    'underscore',
    'Magento_Checkout/js/view/payment/list'
], function (_, Component) {
    'use strict';

    return Component.extend({

        /** @inheritdoc */
        initialize: function () {
            this._super();
        },

        /**
         * Returns payment group title
         *
         * @param {Object} group
         * @returns {String}
         */
        getGroupTitle: function (group) {
            var title = group().title;

            if (group().isDefault() && this.paymentGroupsList().length > 1) {
                title = this.defaultGroupTitle;
            }

            return title;
        },

        /**
         * @param {Object} group
         * @returns {Boolean}
         */
        showFormShared: function (group) {
            var isShow = false;
            if (this.paymentGroupsList().length) {
                if (_.first(this.paymentGroupsList()).alias == group().alias) {
                    isShow = true;
                }
            }
            return isShow;
        }
    });
});
