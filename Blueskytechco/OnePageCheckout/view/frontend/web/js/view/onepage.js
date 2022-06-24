define([
    'uiComponent',
    'uiRegistry',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-billing-address',
    'Blueskytechco_OnePageCheckout/js/model/payment-service',
    'Magento_Checkout/js/model/totals',
    'Blueskytechco_OnePageCheckout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Customer/js/model/customer',
    'Blueskytechco_OnePageCheckout/js/model/update-item-service'
], function (
    Component,
    registry,
    _,
    quote,
    selectBillingAddress,
    paymentService,
    totalsService,
    setShippingInformationAction,
    shippingService,
    customer,
    updateItemService
) {
    'use strict';

    return Component.extend({

        /** @inheritdoc */
        initialize: function () {
            this._super();
            var self = this;
            quote.shippingMethod.subscribe(function (method) {
                if (method && !updateItemService.hasUpdateResult()) {
                    var shippingRates = shippingService.getShippingRates();
                    var availableRate = _.find(shippingRates(), function (rate) {
                        if (rate['method_code'] === null && method['method_code'] === null) {
                            return false;
                        }
                        return rate['carrier_code'] + '_' + rate['method_code'] === method['carrier_code'] + '_' + method['method_code'];
                    });
                    if (availableRate) {
                        if (self._isAddressSameAsShipping()) {
                            selectBillingAddress(quote.shippingAddress());
                        }
                        paymentService.isLoading(true);
                        totalsService.isLoading(true);
                        setShippingInformationAction().done(
                            function () {
                                paymentService.isLoading(false);
                                totalsService.isLoading(false);
                            }
                        );
                    }
                }
            }, this);
            if (!customer.isLoggedIn()) {
                quote.shippingAddress.subscribe(function (address) {
                    if (_.isUndefined(address.street) || address.street.length == 0) {
                        address.street = ["", ""];
                    }
                }, this);
            }


        },

        /**
         * @returns {*}
         */
        _isAddressSameAsShipping: function () {
            return registry.get('checkout.steps.billing-step.payment.payments-list.billing-address-form-shared').isAddressSameAsShipping();
        }
    });
});
