define([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    $.widget('mgsAjaxCartStockOption', {
    options: {
    },
    _create: function () {
        this._bind();
    },
    _bind: function() {
        var options = this.options;
        var select = $('[class^="super-attribute"]');
        var id = $('[name="selected_configurable_option"]');
        var self = this;

        setTimeout(function() {
            select.on("change", function() {
            if(typeof options[id.val()] !== 'undefined') {
                if (options[id.val()].is_in_stock == 0) {
                    self._toggleShow(0);
                } else {
                    self._toggleShow(1);
                }
            }
        })}, 300);

    },
    _toggleShow: function (inStock) {
        if (inStock) {
            $('.box-tocart').removeClass('no-display');
        } else if(!inStock) {
            $('.box-tocart').addClass('no-display');
        }
    }

});
return $.mgsAjaxCartStockOption;

});