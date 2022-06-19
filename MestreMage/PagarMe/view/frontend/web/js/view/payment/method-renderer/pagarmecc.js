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

        function getCcAvailableFlags(flag) {
            var img =  flag.toLowerCase();
            var obj =  window.checkoutConfig.payment.pagarmecc.icons;
            return obj[img].url;
        }
        function maskValue(valor) {
            valor = valor.toString().replace(/\D/g,"");
            valor = valor.toString().replace(/(\d)(\d{8})$/,"$1.$2");
            valor = valor.toString().replace(/(\d)(\d{5})$/,"$1.$2");
            valor = valor.toString().replace(/(\d)(\d{2})$/,"$1,$2");
            return valor

        }

        return Component.extend({
            defaults: {
                template: 'MestreMage_PagarMe/payment/pagarmecc',
                creditCardType: '',
                creditCardExpYear: '',
                creditCardExpMonth: '',
                creditCardNumber: '',
                creditCardSsStartMonth: '',
                creditCardSsStartYear: '',
                creditCardSsIssue: '',
                creditCardVerificationNumber: '',
                selectedCardType: null
            },

            getCode: function() {
                return 'pagarmecc';
            },

            initObservable: function () {
                this._super()
                    .observe([
                        'creditCardType',
                        'creditCardExpYear',
                        'creditCardExpMonth',
                        'creditCardNumber',
                        'creditCardVerificationNumber',
                        'creditCardSsStartMonth',
                        'creditCardSsStartYear',
                        'creditCardSsIssue',
                        'selectedCardType'
                    ]);

                return this;
            },

            initialize: function () {
                this._super();

                var self = this;
                //Set credit card number to credit card data object
                this.creditCardNumber.subscribe(function (value) {
                    var result;

                    self.selectedCardType(null);

                    if (value === '' || value === null) {
                        return false;
                    }
                    result = cardNumberValidator(value);

                    if (!result.isPotentiallyValid && !result.isValid) {
                        return false;
                    }

                    if (result.card !== null) {
                        self.selectedCardType(result.card.type);
                        creditCardData.creditCard = result.card;
                    }

                    if (result.isValid) {
                        creditCardData.creditCardNumber = value;
                        self.creditCardType(result.card.type);
                    }
                });

                //Set expiration year to credit card data object
                this.creditCardExpYear.subscribe(function (value) {
                    creditCardData.expirationYear = value;
                });

                //Set expiration month to credit card data object
                this.creditCardExpMonth.subscribe(function (value) {
                    creditCardData.expirationMonth = value;
                });

                //Set cvv code to credit card data object
                this.creditCardVerificationNumber.subscribe(function (value) {
                    creditCardData.cvvCode = value;
                });


            },
            getCvvImageUrl: function () {
                return window.checkoutConfig.payment.pagarmecc.image_cvv;
            },

            getCvvImageHtml: function () {
                return '<img src="' + this.getCvvImageUrl() +
                    '" alt="Referencia visual do CVV" title="Referencia visual do CVV" />';
            },
            getCcAvailableTypes: function() {
                return window.checkoutConfig.payment.this.item.method.ccavailableTypes;
            },



            selectPaymentMethod: function() {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            },

            getPublickey: function() {

                return window.checkoutConfig.payment.pagarmecc.publickey;
            },

            getIcons: function (type) {
                return window.checkoutConfig.payment.pagarmecc.icons.hasOwnProperty(type) ?
                    window.checkoutConfig.payment.pagarmecc.icons[type]
                    : false;
            },
            getCcAvailableTypesValues: function () {

                return _.map(window.checkoutConfig.payment.pagarmecc.ccavailabletypes, function (value, key) {
                    return {
                        'value': key,
                        'type': value
                    };
                });
            },
            getCcYearsValues: function () {
                return _.map(window.checkoutConfig.payment.pagarmecc.years, function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    };
                });
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
            getCcMonthsValues: function () {
                return _.map(window.checkoutConfig.payment.pagarmecc.months, function (value, key) {
                    return {
                        'value': key,
                        'month': value
                    };
                });
            },
            isActive :function(){
                return true;
            },

            getInstallmentsActive: ko.computed(function () {
                return 1;
            }),

            getTypeViewCreditCard: ko.computed(function () {
                return window.checkoutConfig.payment.pagarmecc.type_view_credit_card;
            }),
            getCustomScript: function () {
                $('.cardmemory').on('click', function(){

                    var type_view_credit_card = window.checkoutConfig.payment.pagarmecc.type_view_credit_card;

                    if(type_view_credit_card == 1) {
                        $('#pagarmecc_fullname').val(this.getAttribute('name-card')).trigger('keyup');
                        $('#pagarmecc_cc_number').val(this.getAttribute('number-card')).trigger('keyup');
                        $('#pagarmecc_expiration').val(this.getAttribute('month-card')).trigger('change');
                        $('#pagarmecc_expiration_yr').val(this.getAttribute('year-card')).trigger('change');
                    }else if(type_view_credit_card == 2){
                        $('#pagarmecc_cc_number').val(this.getAttribute('number-card')).trigger('keyup');
                        $('#pagarmecc_fullname').val(this.getAttribute('name-card')).trigger('keyup');
                        $('#pagarmecc_mm_yy').val(this.getAttribute('month-card')+' / '+this.getAttribute('year-card')).trigger('keyup');

                        $('#pagarmecc_cc_cid').focus();

                        $(".jp-card-container").hide();
                    }

                });

                $('.btndeletedhistorycard').on('click', function(){
                    var objdelhustory =  $('#pagarmecc_deletehistorycard');
                    objdelhustory.val(objdelhustory.val()+this.getAttribute('id-card')+',');
                    $('li#history-'+this.getAttribute('id-card')).css("display","none");
                });

            },


            getInstallments: function () {
                var info_interest = window.checkoutConfig.payment.pagarmecc.info_interest;
                var temp = _.map(info_interest['installments'], function (value) {
                    return {
                        'value': value['installment'],
                        'installments':  value['installment']+' x '+ maskValue(value['installment_amount'])+' total : ' + maskValue(value['amount'])
                    };

                });
                var newArray = [];
                for (var i = 0; i < temp.length; i++) {

                    if (temp[i].installments!='undefined' && temp[i].installments!=undefined) {
                        newArray.push(temp[i]);
                    }
                }

                return newArray;
            },



            getData: function () {
                var array = new Array();
                var cc_exp_month,cc_exp_year,cc_cid,cc_type,cc_number,x;


                if(this.getTypeViewCreditCard() == 2){
                    x = $('#pagarmecc_mm_yy').val();
                    x = x.replace(/\s/g,'');
                    array = x.split('/');
                    cc_exp_month = array[0];
                    cc_exp_year = array[1];

                    cc_cid = $('#pagarmecc_cc_cid').val();
                    cc_type = this.creditCardType();
                    cc_number =  $('#pagarmecc_cc_number').val().replace(/\s/g,'');

                }else{
                    cc_exp_month = this.creditCardExpMonth();
                    cc_exp_year = this.creditCardExpYear();

                    cc_cid = this.creditCardVerificationNumber();
                    cc_type = this.creditCardType();
                    cc_number = this.creditCardNumber();
                }

                return {
                    'method': this.item.method,
                    'additional_data': {
                        'cc_number': cc_number,
                        'cc_type': cc_type,
                        'cc_exp_month': cc_exp_month,
                        'cc_exp_year': cc_exp_year,
                        'cc_cid': cc_cid,
                        'fullname': jQuery('#'+this.getCode()+'_fullname').val(),
                        'installments': jQuery('#'+this.getCode()+'_installments').val(),
                        'pagarmecc_cpf': jQuery('#pagarmecc_cpf').val()
                    }
                };
            },

            validate: function() {
                var $form = $('#' + this.getCode() + '-form');

                return $form.validation() && $form.validation('isValid');
            }
        });
    }
);
