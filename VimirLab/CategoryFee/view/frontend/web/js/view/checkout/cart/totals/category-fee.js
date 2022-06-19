/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'VimirLab_CategoryFee/js/view/checkout/summary/category-fee'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            }
        });
    }
);