/**
 * Copyright © 2021 Magento. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiComponent', 
    'uiRegistry',
    'underscore'
], function ($, Component, registry, _) {
    'use strict';
    $.Product = function (data) {
        this.name = data.name;
        this.image = data.image;
        this.reviews_rating = data.reviews_rating;
        this.short_description = data.short_description;
        this.price = data.price;
        this.url = data.url;
    };
    return Component.extend({
        defaults: {
            searchText: ''
        },

        initialize: function () {
            this._super();
        },

        load: function () {
            var self = this;

            if (this.xhr) {
                this.xhr.abort();
            }
			if(this.searchCate){
				var data_search = {q: this.searchText,cat: this.searchCate};
			}else{
				var data_search = {q: this.searchText};
			}

            this.xhr = $.ajax({
                method: "get",
                dataType: "json",
                url: this.url,
                data: data_search,
                beforeSend: function () {
                    self.spinnerShow();
                },
                success: $.proxy(function (response) {
                    self.parseData(response);
                    self.spinnerHide();
                    self.showPopup(); 
                })
            });
        },

        load_suggest_product: function () {
            var self = this;

            if (this.xhr) {
                this.xhr.abort();
            }

            this.xhr = $.ajax({
                method: "get",
                dataType: "json",
                url: this.url_suggest_product,
                success: $.proxy(function (response) {
                    self.parseData(response);
                    self.showPopupSuggestProduct(); 
                })
            });
        },

        showPopupSuggestProduct: function () {
            registry.get('autocompleteBindEvents', function (binder) {
                binder.showPopupSuggestProduct();
            });
        },

        showPopup: function () {
            registry.get('autocompleteBindEvents', function (binder) {
                binder.showPopup();
            });
        },

        spinnerShow: function () {
            registry.get('autocompleteBindEvents', function (binder) {
                binder.spinnerShow();
            });
        },

        spinnerHide: function () {
            registry.get('autocompleteBindEvents', function (binder) {
                binder.spinnerHide();
            });
        },

        parseData: function (response) {
            this.setProducts(this.getResponseData(response, 'product'));
        },

        getResponseData: function (response, code) {
            var data = []
            if (_.isUndefined(response.result)) {
                return data;
            }
            $.each(response.result, function (index, obj) {
                if (index == code) {
                    data = obj;
                }
            });
            return data;
        },

        setProducts: function (productsData) {
            var products = [];

            if (!_.isUndefined(productsData.data)) {
                products = $.map(productsData.data, function (product) {
                    return new $.Product(product) });
            }

            registry.get('searchsuite_autocomplete_form', function (autocomplete) {
                autocomplete.result.product.data(products);
                autocomplete.result.product.size(productsData.size);
                autocomplete.result.product.url(productsData.url);
                autocomplete.result.product.text_suggestion(productsData.text_suggestion);
            });
        },

        _hash: function (object) {
            var string = JSON.stringify(object) + "";

            var hash = 0, i, chr, len;
            if (string.length == 0) {
                return hash;
            }
            for (i = 0, len = string.length; i < len; i++) {
                chr = string.charCodeAt(i);
                hash = ((hash << 5) - hash) + chr;
                hash |= 0;
            }
            return 'h' + hash;
        }

    });
});
