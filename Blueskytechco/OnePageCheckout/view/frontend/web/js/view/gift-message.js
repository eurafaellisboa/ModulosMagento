define([
    'ko',
    'uiComponent',
    'Blueskytechco_OnePageCheckout/js/model/gift-message',
    'Blueskytechco_OnePageCheckout/js/action/gift-options'
], function (ko, Component, GiftMessage, giftOptionsService) {
    'use strict';

    return Component.extend({
        formBlockVisibility: null,
        resultBlockVisibility: null,
        model: {},
        isLoading: ko.observable(false),

        /**
         * Component init
         */
        initialize: function () {
            var self = this,
                model;
            this._super()
                .observe('formBlockVisibility')
                .observe({
                    'resultBlockVisibility': false
                });
            this.itemId = this.itemId || 'orderLevel';
            this.model = new GiftMessage(this.itemId);
            this.model.getObservable('isClear').subscribe(function (value) {
                if (value == true) {
                    self.formBlockVisibility(false);
                    self.model.getObservable('alreadyAdded')(true);
                }
            });
            this.model.afterSubmit = function () {
                self.hideFormBlock();
                self.resultBlockVisibility(false);
                self.isLoading(false);
            };
            this.isResultBlockVisible();
        },

        /**
         * Is reslt block visible
         */
        isResultBlockVisible: function () {
            var self = this;

            if (this.model.getObservable('alreadyAdded')()) {
                this.resultBlockVisibility(true);
            }
            this.model.getObservable('additionalOptionsApplied').subscribe(function (value) {
                if (value == true) { //eslint-disable-line eqeqeq
                    self.resultBlockVisibility(true);
                }
            });
        },

        /**
         * @param {String} key
         * @return {*}
         */
        getObservable: function (key) {
            return this.model.getObservable(key);
        },

         /**
         * Hide\Show form block
         */
        toggleFormBlockVisibility: function () {
            if (!this.model.getObservable('alreadyAdded')()) {
                this.formBlockVisibility(!this.formBlockVisibility());
            } else {
                this.resultBlockVisibility(!this.resultBlockVisibility());
            }
        },

        /**
         * Edit options
         */
        editOptions: function () {
            this.resultBlockVisibility(false);
            this.formBlockVisibility(true);
        },

        /**
         * Delete options
         */
        deleteOptions: function () {
            this.isLoading(true);
            giftOptionsService(this.model);
            this.model.getObservable('alreadyAdded')(false);
        },

        /**
         * Hide form block
         */
        hideFormBlock: function () {
            this.formBlockVisibility(false);

            if (this.model.getObservable('alreadyAdded')()) {
                this.resultBlockVisibility(true);
            }
        },

        /**
         * @return {Boolean}
         */
        hasActiveOptions: function () {
            var regionData = this.getRegion('additionalOptions'),
                options = regionData(),
                i;

            for (i = 0; i < options.length; i++) {
                if (options[i].isActive()) {
                    return true;
                }
            }

            return false;
        },

        /**
         * @return {Boolean}
         */
        isActive: function () {
            return this.model.isGiftMessageAvailable();
        },

        /**
         * Submit options
         */
        submitOptions: function () {
            this.isLoading(true);
            giftOptionsService(this.model);
            this.model.getObservable('alreadyAdded')(true);
        }
    });
});
