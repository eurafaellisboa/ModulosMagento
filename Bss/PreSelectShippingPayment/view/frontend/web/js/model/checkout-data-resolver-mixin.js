/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_PreSelectShippingPayment
 * @author    Extension Team
 * @copyright Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */

define([
    'underscore',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/action/select-payment-method'
],function (_, checkoutData, paymentService, selectShippingMethodAction, selectPaymentMethodAction) {

    'use strict';

    return function (checkoutDataResolver) {
        var checkoutDataResolverShipping = checkoutDataResolver.resolveShippingRates,
            checkoutDataResolverPayment = checkoutDataResolver.resolvePaymentMethod,
            checkoutConfig = window.checkoutConfig;
        return _.extend(checkoutDataResolver, {
            /**
             * @inheritdoc
             */
            resolveShippingRates: function (ratesData) {
                var selectedShippingRate = checkoutData.getSelectedShippingRate();
                if (!_.isUndefined(checkoutConfig.bssAspConfig) &&
                    !_.isUndefined(checkoutConfig.bssAspConfig.shipping) &&
                    !selectedShippingRate &&
                    ratesData &&
                    ratesData.length > 1
                ) {
                    var defaultShipping = checkoutConfig.bssAspConfig.shipping.default,
                        positionShipping = checkoutConfig.bssAspConfig.shipping.position;
                    this._autoSelect(defaultShipping, positionShipping, ratesData, 'shipping');
                }
                
                return checkoutDataResolverShipping.apply(this, arguments);
            },

            /**
             * @inheritdoc
             */
            resolvePaymentMethod: function () {
                var availablePaymentMethods = paymentService.getAvailablePaymentMethods(),
                    selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();

                console.log(availablePaymentMethods);
                if (!_.isUndefined(checkoutConfig.bssAspConfig) &&
                    !_.isUndefined(checkoutConfig.bssAspConfig.payment) &&
                    !selectedPaymentMethod &&
                    availablePaymentMethods &&
                    availablePaymentMethods.length > 1
                ) {
                    var defaultPayment = checkoutConfig.bssAspConfig.payment.default,
                        positionPayment = checkoutConfig.bssAspConfig.payment.position;
                    this._autoSelect(defaultPayment, positionPayment, availablePaymentMethods, 'payment');
                }
                return checkoutDataResolverPayment.apply(this);
            },

            /**
             * Auto select with config
             * @param {String} defaultMethod
             * @param {String} positionMethod
             * @param {String} availableMethods
             * @param {String} step
             */
            _autoSelect: function (defaultMethod, positionMethod, availableMethods, step) {
                if (!_.isUndefined(defaultMethod) && defaultMethod) {
                    var selectMethod = '';
                    availableMethods.some(function (method) {
                        if ((step === 'payment' && method.method === defaultMethod) ||
                            (step === 'shipping' && (method.carrier_code + '_' + method.method_code === defaultMethod))
                        ) {
                            selectMethod = method;
                        }
                    });
                }

                if (selectMethod) {
                    if (step === 'shipping') {
                        selectShippingMethodAction(selectMethod);
                    } else {
                        selectPaymentMethodAction(selectMethod);
                    }
                } else {
                    var position = '';
                    if (positionMethod === 'last') {
                        availableMethods.some(function (method, index) {
                            if (method['method_code'] !== null) {
                                position = index;
                            }
                        });
                    } else if (positionMethod === 'first') {
                        var count = 0;
                        availableMethods.some(function (method, index) {
                            if (method['method_code'] !== null) {
                                count++;
                                if (count == 1) {
                                    position = index;
                                }
                            }
                        });
                    }

                    if (position !== '') {
                        if (step === 'shipping') {
                            selectShippingMethodAction(availableMethods[position]);
                        } else {
                            selectPaymentMethodAction(availableMethods[position]);
                        }
                    }
                }
            }
        });
    };
});