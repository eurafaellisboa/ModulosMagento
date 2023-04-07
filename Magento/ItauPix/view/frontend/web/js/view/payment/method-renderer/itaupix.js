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
        'Magento_Checkout/js/model/payment/additional-validators',
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
        additionalValidators,
        url,
        calendar) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_ItauPix/payment/itaupix'
            },
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'itaupixpix_cpf': jQuery('#'+this.getCode()+'_cpf').val()
                    }
                };
            },
            getCode: function() {
                return 'itaupix';
            },
            maskCpfCnpj: function() {
                var cpf = jQuery('#'+this.getCode()+'_cpf');
                var v = cpf.val();
                if (v.length <= 14) {
                    v=v.replace(/\D/g,"");
                    v=v.replace(/(\d{3})(\d)/,"$1.$2");
                    v=v.replace(/(\d{3})(\d)/,"$1.$2");
                    v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2");
                }else{
                    v=v.replace(/\D/g,"")
                    v=v.replace(/^(\d{2})(\d)/,"$1.$2");
                    v=v.replace(/^(\d{2})\.(\d{3})(\d)/,"$1.$2.$3");
                    v=v.replace(/\.(\d{3})(\d)/,".$1/$2");          
                    v=v.replace(/(\d{4})(\d)/,"$1-$2");
                }

                cpf.val(v);

            },
            getInstructionsPix: function() {
                return window.checkoutConfig.payment.itaupix.instructions_checkout_pix;
            },
            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            }
        });
    }
);
