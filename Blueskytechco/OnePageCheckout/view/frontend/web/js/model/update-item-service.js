define([
    'ko'
], function (ko) {
    'use strict';

    var hasUpdateResult = ko.observable(false);

    return {
        hasUpdateResult : hasUpdateResult
    }
});
