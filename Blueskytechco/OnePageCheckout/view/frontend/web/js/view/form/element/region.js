define([
    'uiRegistry',
    'Magento_Ui/js/form/element/region'
], function (registry, Component) {
    'use strict';

    return Component.extend({

        /**
         * @inheritdoc
         */
        filter: function (value, field) {
            var country = registry.get(this.parentName + '.' + 'country_id');

            if (country) {
                this._super(value, field);
            }
        }
    });
});
