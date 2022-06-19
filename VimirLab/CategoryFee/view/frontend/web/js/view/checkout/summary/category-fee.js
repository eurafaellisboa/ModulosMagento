/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
		'jquery',
        'mage/url',
		'mage/translate',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function ($, url, mage, Component, quote, priceUtils, totals) {
        "use strict";
		
        return Component.extend({
            defaults: {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'VimirLab_CategoryFee/checkout/summary/category-fee'
            },
            totals: quote.getTotals(),
            categoryFeeLabel: $.mage.__('Taxa de visita'),
            isDisplayed: function() {
				var price = 0;
                if (this.totals() && totals.getSegment('category_fee')) {
                    price = totals.getSegment('category_fee').value;
                }
				if(price == 0){
					return false;
				}
            },
			isDisplayCategoryFeeLabelTotal: function() {
				var price = 0;
                if (this.totals() && totals.getSegment('category_fee')) {
                    price = totals.getSegment('category_fee').value;
                }
				if(price == 0){
					return false;
				}
                return true;
            },
            getCategoryFeeTotal: function() {
                var price = 0;
                if (this.totals() && totals.getSegment('category_fee')) {
                    price = totals.getSegment('category_fee').value;
                }
                return this.getFormattedPrice(price);
            },
            getTotalSummaryLabel : function () {
                return this.categoryFeeLabel ? this.categoryFeeLabel : '';
            }
        });
    }
);