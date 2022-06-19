/*browser:true*/
/*global define*/
define(
    [
        'underscore',
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Payment/js/model/credit-card-validation/credit-card-data',
        'Magento_Payment/js/model/credit-card-validation/validator',
        'Magento_Checkout/js/model/payment/additional-validators',
        'MestreMage_PagarMe/js/model/credit-card-validation/credit-card-number-validator',
        'MestreMage_PagarMe/js/model/credit-card-validation/custom',
        'mage/url',
        'mage/calendar',
        'mage/translate'
    ],
    function (
        _,
        $,
        ko,
        quote,
        priceUtils,
        Component,
        placeOrderAction,
        selectPaymentMethodAction,
        customer,
        checkoutData,
        creditCardData,
        validator,
        additionalValidators,
        cardNumberValidator,
        custom,
        url,
        calendar) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'MestreMage_PagarMe/payment/pagarmebl'
            },
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'pagarmebl_cpf': jQuery('#'+this.getCode()+'_cpf').val()
                    }
                };
            },
            getCode: function() {
                return 'pagarmebl';
            },
            mascarJsr: function() {
                var cpf = jQuery('#'+this.getCode()+'_cpf');
                var v = cpf.val();
                v=v.replace(/\D/g,"");
                v=v.replace(/(\d{3})(\d)/,"$1.$2");
                v=v.replace(/(\d{3})(\d)/,"$1.$2");
                v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2");

                cpf.val(v);

            },
            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            }
        });
    }
);
