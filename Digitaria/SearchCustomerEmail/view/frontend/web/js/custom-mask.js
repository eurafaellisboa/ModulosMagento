define([
    'jquery',
    'inputmask',
    'Magento_Ui/js/modal/alert',
    'Magento_Framework_Stdlib_Json/json2'
], function ($, inputmask, alert, JSON) {
    'use strict';

    var maskEnabled = window.searchCustomerEmailConfig.maskEnabled;    
    $(document).ready(function () {

        var SPMaskBehavior = function (val) {
            return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-0099';
        };

        var spOptions = {
            onKeyPress: function (val, e, field, options) {
                field.mask(SPMaskBehavior(val), options);
            }
        };

        if (maskEnabled) {
            $('#taxvat').mask(SPMaskBehavior, spOptions);
        }

        $('#search-customer-email-form').submit(function (event) {
            event.preventDefault();
            var form = $(this);
            var resultContainer = $('#search-customer-email-result');

            $.ajax({
                url: form.attr('action'),
                type: 'post',
                dataType: 'json',
                data: form.serialize(),
                beforeSend: function () {
                    resultContainer.html('<p>Loading...</p>');
                },
                success: function (response) {
                    if (response.success) {
                        resultContainer.html('<p>The registered email for the taxvat is: <span id="search-customer-email">' + response.email + '</span></p>');
                    } else {
                        resultContainer.html('<p>Email not found for this taxvat</p>');
                    }
                },
                error: function () {
                    resultContainer.html('<p>An error occurred while processing the request. Please try again.</p>');
                }
            });
        });
    });
});
