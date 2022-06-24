
define(['jquery'], function ($) {
    'use strict';
    return function (target) {
        target.getObservableFields = function () {
            var self = this,
                observableFields = [];

            $.each(self.getRules(), function (carrier, fields) {
                $.each(fields, function (field) {
                    if (observableFields.indexOf(field) === -1) {
                        observableFields.push(field);
                    }
                });
            });
            observableFields.push('telephone'); // Load shipping method on Phone Number change
            return observableFields;
        };
        return target;
    };
});
