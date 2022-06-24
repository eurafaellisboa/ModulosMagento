define([
    'moment',
    'Magento_Ui/js/form/element/date',
    'Blueskytechco_OnePageCheckout/js/lib/jquery/jquery-ui-addon-slider-access'
], function (moment, coreDate) {
    'use strict';

    return coreDate.extend({
        defaults: {
            options: {
                addSliderAccess: true,
                sliderAccessArgs: { touchonly: false }
            },
        },

        /**
         * Prepares and sets date/time value that will be sent
         * to the server.
         *
         * @param {String} shiftedValue
         */
        onShiftedValueChange: function (shiftedValue) {
            var value,
                formattedValue,
                momentValue;

            if (shiftedValue) {
                momentValue = moment(shiftedValue, this.pickerDateTimeFormat);

                if (this.options.showsTime) {
                    formattedValue = moment(momentValue).format(this.timezoneFormat);
                    value = moment.tz(formattedValue, this.storeTimeZone).tz('UTC').toISOString();
                } else {
                    value = momentValue.format(this.outputDateFormat);
                }
            } else {
                value = '';
            }
            if (value !== this.value() && !this.options.showsTime) {
                this.value(value);
            }
        },
    });
});
