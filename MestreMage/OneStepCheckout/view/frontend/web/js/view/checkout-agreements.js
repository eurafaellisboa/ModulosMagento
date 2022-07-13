/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'jquery',
    'uiComponent',
    'MestreMage_OneStepCheckout/js/model/agreements-modal',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/set-shipping-information'
], function (ko, $, Component, agreementsModal, customer, quote, setShippingInformationAction) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        agreementManualMode = 1,
        agreementsConfig = checkoutConfig ? checkoutConfig.checkoutAgreements : {};

    return Component.extend({
        defaults: {
            template: 'MestreMage_OneStepCheckout/checkout/checkout-agreements'
        },
        isVisible: agreementsConfig.isEnabled,
        agreements: agreementsConfig.agreements,
        modalTitle: ko.observable(null),
        modalContent: ko.observable(null),
        modalWindow: null,

        /**
         * Checks if agreement required
         *
         * @param {Object} element
         */
        isAgreementRequired: function (element) {
            return element.mode == agreementManualMode; //eslint-disable-line eqeqeq
        },

        /**
         * Show agreement content in modal
         *
         * @param {Object} element
         */
        showContent: function (element) {
            this.modalTitle(element.checkboxText);
            this.modalContent(element.content);
            agreementsModal.showModal();
        },

        /**
         * build a unique id for the term checkbox
         *
         * @param {Object} context - the ko context
         * @param {Number} agreementId
         */
        getCheckboxId: function (context, agreementId) {
            var paymentMethodName = '',
                paymentMethodRenderer = context.$parents[1];

            // corresponding payment method fetched from parent context
            if (paymentMethodRenderer) {
                // item looks like this: {title: "Check / Money order", method: "checkmo"}
                paymentMethodName = paymentMethodRenderer.item ?
                  paymentMethodRenderer.item.method : '';
            }

            return 'agreement_' + paymentMethodName + '_' + agreementId;
        },

        /**
         * Init modal window for rendered element
         *
         * @param {Object} element
         */
        initModal: function (element) {
            agreementsModal.createModal(element);
        },

        changeHandler: function(data, event){
            $("#checkout-payment-method-load input[name='agreement[" + data.agreementId + "]']").click();
            $(event.target).next().click();
            return true;
        },
        validateForm: function (form) {
            return $(form).validation() && $(form).validation('isValid');
        },

        placeOrder: function(){
            this.validPaymentChecked();
            if(!this.notValidShipping()){
                if(this.validateForm('#checkout_agreements_block')){
                    if ($('#submit-shipping-method').length) {//if not virtual product
                        customer.placeorder = true;
                        this.setBillingForced();
                        if($('input#moipcc').length){
                            $("#co-payment-form ._active button[type='submit']").click();
                        }else{
                            $('#submit-shipping-method').click();
                        }
                    } else {
                        if ($("#co-payment-form ._active input[name='billing-address-same-as-shipping']:first").is(':checked')) {
                            quote.billingAddress(quote.shippingAddress());
                        }
                        $("#co-payment-form ._active button[type='submit']").click();
                    }
                }
            }
        },

        setBillingForced: function(){
            if(!customer.isLoggedIn()){
                if($("#co-payment-form ._active input[type='checkbox']").prop('checked') == true || $("#co-payment-form .billing-address-same-as-shipping-block input[type='checkbox']").prop('checked') == true){
                    var count_loop = 0;
                    var loop_interval = setInterval(function(){
                        quote.billingAddress(quote.shippingAddress());
                        setShippingInformationAction();
                    if(count_loop++ >= 1){
                        clearInterval(loop_interval)
                    }
                    },100)

                }
        }
        },

        validPaymentChecked: function(){
            $('button.action.action-apply').prop('type','');
            $('button.action.action-cancel').prop('type','');
            
            var html_msg = '<div role="alert" class="message message-error error">' +
                '<div>Por favor, Selecione uma forma de pagamento.</div>' +
                ' </div>';

            var checked = false;
            $(".checkout-payment-method .payment-method-title input").each(function(i){
                if(jQuery(this).is(":checked")){
                    checked = true;
                }
            });

            if(checked){
                $('#mm_msg_payment').detach();
            }else{
                if(!$('#mm_msg_payment').length){
                    $('<div id="mm_msg_payment">'+html_msg+'</div>').insertAfter('.osc .checkout-payment-method .step-title');
                }
            }
        },

        notValidShipping: function(){
            if(customer.isLoggedIn()){
                return 0;
            }
            var count = 0;
            $('div#shipping-new-address-form ._required input').each(function(){
                if($(this).attr('name') != 'region'){
                    if($(this).val() == ''){
                        $(this).val('1');
                        $(this).trigger('keyup');
                        $(this).val('');
                        $(this).trigger('keyup');
                        count++;
                    }
                }
            });
            return count;
        }
    });

});
