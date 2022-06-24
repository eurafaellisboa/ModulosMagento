/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'uiComponent',
    'ko'
], function ($, Component, ko) {
    'use strict';


    return Component.extend({
        defaults: {
            template: 'Blueskytechco_SearchSuite/search/autocomplete',
            showPopup: ko.observable(false),
            result: {
                product: {
                    data: ko.observableArray([]),
                    size: ko.observable(0),
                    url: ko.observable(''),
                    text_suggestion: ko.observable('') 
                }
            },
            textNoResult: '',
            anyResultCount: false
        },


        initialize: function () {
            var self = this;
            this._super();
            this.anyResultCount = ko.computed(function () {
                var sum = self.result.product.size();
                if (sum > 0) {
                    return true; }
                return false;
            }, this);

        }
    });
});