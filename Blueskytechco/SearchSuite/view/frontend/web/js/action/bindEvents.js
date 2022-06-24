/**
 * Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
 define([
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mageUtils'
], function ($, Component, registry, utils) {
    'use strict';

    return Component.extend({

        initialize: function () {
            this._super();
            utils.limit(this, 'load', this.searchDelay); // execute 'load' after delay
            // this is the id of the form
            $(this.searchFormSelector).attr('action', this.url);
            $(this.inputSelector)
                .unbind('input') // unbind all magento events
                .on('input', $.proxy(this.load, this)) // bind searchsuiteautocomplete load event
                .on('input', $.proxy(this.searchButtonStatus, this)) // bind show/hide search button event
                .on('focus', $.proxy(this.showPopup, this)); // bind show popup event
			$(this.inputSelectorCate)
                .unbind('select') // unbind all magento events
                .on('change', $.proxy(this.load, this)); // bind searchsuiteautocomplete load event
            $(this.searchClear)
                .on('click', $.proxy(this.clearSearch, this));
			var suggestProduct = this.suggestProduct;
            var typeSearch = this.typeSearch;
            $(document).on("click", function(event){
				var $trigger = $("#search-form");
                if(typeSearch == 'defaut' || typeSearch == 'dropdown'){
                    if($trigger !== event.target && !$trigger.has(event.target).length){
                        registry.get('searchsuite_autocomplete_form', function (autocomplete) {
                            autocomplete.showPopup(false);
                        });
                    }
                }
			});	

            $(document).ready($.proxy(this.load_search, this)); // load autocomplete data on catalogsearch page after submit search button
            $(document).ready($.proxy(this.searchButtonStatus, this));
        },

        load: function (event) {
            var self = this;
            var searchText = $(self.inputSelector).val();
			var searchCate = $(self.inputSelectorCate).val();
            var searchClear = $(self.searchClear);

            if (searchText.length > 0) {
                searchClear.removeClass('hidden');
            }else{
                if (!searchClear.hasClass('hidden')) {
                    searchClear.addClass('hidden');
                }
            }
            
            if (searchText.length < self.getMinQueryLength) {
                return false;
            }

            registry.get('autocompleteDataProvider', function (dataProvider) {
                dataProvider.searchText = searchText;
				dataProvider.searchCate = searchCate;
                dataProvider.load();
            });
        },

        clearSearch: function (event) {
            var self = this;
            var searchText = $(self.inputSelector);
            var searchClear = $(self.searchClear);
            searchText.val('');
            if (!searchClear.hasClass('hidden')) {
                searchClear.addClass('hidden');
            }
            searchText.focus();
            registry.get('searchsuite_autocomplete_form', function (autocomplete) {
                autocomplete.showPopup(false);
            });
        },

        load_search: function (event) {
            var self = this;
            var searchText = $(self.inputSelector).val();
			var searchCate = $(self.inputSelectorCate).val();

            if (searchText.length < self.getMinQueryLength) {
                if(self.urlSuggestProduct){
                    registry.get('autocompleteDataProvider', function (dataProvider) {
                        dataProvider.url_suggest_product = self.urlSuggestProduct;
                        dataProvider.load_suggest_product();
                    });
                }else{
                    return false;
                }
                
            }else{
                registry.get('autocompleteDataProvider', function (dataProvider) {
                    dataProvider.searchText = searchText;
                    dataProvider.searchCate = searchCate;
                    dataProvider.load();
                });
            }
        },

        showPopup: function (event) {
            var self = this,
                searchField = $(self.inputSelector),
                searchFieldHasFocus = searchField.val().length >= self.getMinQueryLength;
            if(searchFieldHasFocus){
                $('.auto-complete-result').removeClass('result-suggest-product');
            }
            if(this.suggestProduct == 'true' && this.typeSearch == 'canvas'){
                searchFieldHasFocus = true;
            }else if($('.auto-complete-result').hasClass('result-suggest-product')){
                searchFieldHasFocus = true;
            }
            registry.get('searchsuite_autocomplete_form', function (autocomplete) {
                autocomplete.showPopup(searchFieldHasFocus);
            });
        },

        showPopupSuggestProduct: function (event) {
            var self = this;
            var showPopup = false;
            if(this.suggestProduct == 'true' && this.typeSearch == 'canvas'){
                showPopup = true;
            }else{
                $('.auto-complete-result').addClass('result-suggest-product');
            }
            registry.get('searchsuite_autocomplete_form', function (autocomplete) {
                autocomplete.showPopup(showPopup);
            });
        },

        searchButtonStatus: function (event) {
            var self = this,
                searchField = $(self.inputSelector),
                searchButton = $(self.searchButtonSelector),
                searchButtonDisabled = (searchField.val().length > 0) ? false : true;

            searchButton.attr('disabled', searchButtonDisabled);
        },

        spinnerShow: function () {
            var spinner = $(this.searchFormSelector);
            spinner.addClass('loading');
        },

        spinnerHide: function () {
            var spinner = $(this.searchFormSelector);
            spinner.removeClass('loading');
        }

    });
});
