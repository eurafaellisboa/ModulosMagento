define([
    'ko',
    'jquery',
    'uiComponent',
    'uiRegistry',
    'mage/translate',
    'Magento_Checkout/js/model/quote',
    'Blueskytechco_OnePageCheckout/js/model/shipping-rate-processor/new-address',
    'Blueskytechco_OnePageCheckout/js/model/shipping-rate-processor/customer-address',
    'Blueskytechco_OnePageCheckout/js/action/validate-shipping-information',
    // 'Blueskytechco_OnePageCheckout/js/action/validate-gift-wrap-before-order',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'underscore',
    'Magento_Ui/js/modal/alert',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/totals'
], function (
    ko,
    $,
    Component,
    registry,
    $t,
    quote,
    DefaultProcessor,
    CustomerAddressProcessor,
    validateShippingInformationAction,
    // validateGiftWrapAction,
    fullScreenLoader,
    selectBillingAddress,
    additionalValidators,
    shippingService,
    rateRegistry,
    _,
    alert,
    checkoutData,
    totals
) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Blueskytechco_OnePageCheckout/place-order-btn'
        },

        placeOrderLabel: ko.observable($t(window.checkoutConfig.OnePageCheckout.titlePlaceOrder)),

        isVisible: ko.observable(true),

        isPlaceOrderActionAllowed: ko.observable(quote.billingAddress() != null && quote.paymentMethod() != null),

        /** @inheritdoc */
        initialize: function () {
            window.isPlaceOrderDispatched = false;
            this._super();
            var self = this;
            quote.billingAddress.subscribe(function (address) {
                if (quote.isVirtual()) {
                    setTimeout(function () {
                        self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null);
                    }, 500);
                } else {
                    self.isPlaceOrderActionAllowed(address !== null && quote.paymentMethod() != null && quote.shippingMethod() != null);
                }
            }, this);
            quote.paymentMethod.subscribe(function (newMethod) {
                if (quote.isVirtual()) {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null);
                } else {
                    self.isPlaceOrderActionAllowed(newMethod !== null && quote.billingAddress() != null && quote.shippingMethod() != null);
                }

                if ((newMethod.method === "amazonlogin") ||
                    newMethod.method === "braintree_paypal"
                ) {
                    self.isVisible(false);
                }
            }, this);
            if (!quote.isVirtual()) {
                quote.shippingMethod.subscribe(function (method) {
                    var availableRate,
                        shippingRates = shippingService.getShippingRates();
                    if (method) {
                        availableRate = _.find(shippingRates(), function (rate) {
                            return rate['carrier_code'] + '_' + rate['method_code'] === method['carrier_code'] + '_' + method['method_code'];
                        });
                    }
                    self.isPlaceOrderActionAllowed(availableRate && quote.paymentMethod() != null && quote.billingAddress() != null);
                }, this);
            }

            if (
                window.checkoutConfig.paypal_in_context == true || !_.isEmpty(window.checkoutConfig.amazonLogin)
            ) {
                var selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();

                if (selectedPaymentMethod == "paypal_express" ||
                    selectedPaymentMethod == "amazonlogin" ||
                    selectedPaymentMethod == "braintree_paypal") {
                    self.isVisible(false);
                }

                $(document).on('change', '.payment-method .radio', function () {
                    if ($('.payment-method._active').find('.actions-toolbar').is('#paypal-express-in-context-button') ||
                        ($(this).attr('id') === 'amazonlogin') ||
                        ($(this).attr('id') === 'braintree_paypal')
                    ) {
                        self.isVisible(false);
                    } else {
                        self.isVisible(true);
                    }
                });
            }
        },

        placeOrder: function (data, event) {
            var self = this;
            var shippingAddressComponent = registry.get('checkout.steps.shipping-step.shippingAddress');
            window.isPlaceOrderDispatched = true;
            if (event) {
                event.preventDefault();
            }
            if (additionalValidators.validate()) {
                if (quote.isVirtual()) {
                    $('input#' + self.getCode())
                        .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                        .trigger('click');
                } else {
                    if (shippingAddressComponent.validateShippingInformation()) {
                        var reSelectShippingAddress = false;
                        if (typeof window.shippingAddress !== "undefined" || !$.isEmptyObject(window.shippingAddress)) {
                            quote.shippingAddress(window.shippingAddress);
                            var type = quote.shippingAddress().getType();
                            if (type == 'customer-address') {
                                var cache = rateRegistry.get(quote.shippingAddress().getKey());
                                if (!cache) {
                                    reSelectShippingAddress = true;
                                    CustomerAddressProcessor(quote.shippingAddress()).done(function (result) {
                                        self.placeOrderContinue();
                                    }).fail(function (response) {

                                    }).always(function () {
                                        window.shippingAddress = {};
                                    });
                                } else {
                                    window.shippingAddress = {};
                                }
                            } else {
                                var cache = rateRegistry.get(quote.shippingAddress().getCacheKey());
                                if (!cache) {
                                    reSelectShippingAddress = true;
                                    DefaultProcessor(quote.shippingAddress()).done(function (result) {
                                        self.placeOrderContinue();
                                    }).fail(function (response) {

                                    }).always(function () {
                                        window.shippingAddress = {};
                                    });
                                } else {
                                    window.shippingAddress = {};
                                }
                            }
                        }
                        if (!reSelectShippingAddress) {
                            self.placeOrderContinue();
                        }
                    }
                }
            }
            return false;
        },

        placeOrderContinue: function () {
            var self = this;
            var billingAddressComponent = registry.get('checkout.steps.billing-step.payment.payments-list.billing-address-form-shared');

            if (billingAddressComponent.isAddressSameAsShipping()) {
                fullScreenLoader.startLoader();
                selectBillingAddress(quote.shippingAddress());
            }
            validateShippingInformationAction().done(
                function () {
                    fullScreenLoader.stopLoader();
                    $('input#' + self.getCode())
                        .closest('.payment-method').find('.payment-method-content .actions-toolbar:not([style*="display: none"]) button.action.checkout')
                        .trigger('click');
                }
            ).fail(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        },

        getCode: function () {
            return quote.paymentMethod().method;
        }
    });
});
