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
        'MestreMage_Getnet/js/model/credit-card-validation/credit-card-number-validator',
        'MestreMage_Getnet/js/model/credit-card-validation/custom',
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


        if($("#mestremagegn").is(":checked")) {
            (function () {
                const send = XMLHttpRequest.prototype.send;
                XMLHttpRequest.prototype.send = function () {
                    this.addEventListener('load', function () {
                        var url = this.responseURL;

                        if (url.indexOf("payment-information") != -1) {
                            var data = JSON.parse('[' + this.responseText + ']');

                            if (data[0][['message']]) {
                                console.log(data[0][['message']]);
                                alert(data[0][['message']]);
                            }

                        }
                    });
                    return send.apply(this, arguments)
                }
            })();
        }

        function getCcAvailableFlags(flag) {
            var img =  flag.toLowerCase();
            var obj =  window.checkoutConfig.payment.mestremagegn.icons;
            return obj[img].url;
        }

        return Component.extend({
            defaults: {
                template: 'MestreMage_Getnet/payment/cc',
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
                return 'mestremagegn';
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
                return window.checkoutConfig.payment.mestremagegn.image_cvv;
            },

            getCvvImageHtml: function () {
                return '<img src="' + this.getCvvImageUrl() +
                    '" alt="Referencia visual do CVV" title="Referencia visual do CVV" />';
            },
            getCcAvailableTypes: function() {
                return window.checkoutConfig.payment.this.item.method.ccavailableTypes;
            },


            getDetailsCard: function () {

                var json = window.checkoutConfig.payment.mestremagegn.cardmemory;
                var html = '<ul class="ul-history">';
                var data = JSON.parse(json);

                data.forEach(function (linha) {

                    html += '<li  id="history-'+linha['id-card']+'" class="h-card" ><input type="radio" class="cardmemory" name="cardmemory"  id="'+linha['id-card']+'" flag-card="'+linha['flag-card']+'" name-card="'+linha['name-card']+'" number-card="'+linha['number-card']+'" month-card="'+linha['month-card']+'" year-card="'+linha['year-card']+'"><img src="'+getCcAvailableFlags(linha['flag-card'])+' " /> <label for="'+linha['id-card']+'">&ensp;&ensp;&ensp;  '+ linha['name-card']+' &ensp; &ensp; &ensp;  *******'+linha['id-card']+'</label><button type="button"  class="close btndeletedhistorycard"id-card="'+linha['id-card']+'" aria-label="Close"> <span aria-hidden="true">&times;</span> </button></li>';

                });

                return html+'</ul>';
            },

            selectPaymentMethod: function() {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            },

            getPublickey: function() {

                return window.checkoutConfig.payment.mestremagegn.publickey;
            },

            getIcons: function (type) {
                return window.checkoutConfig.payment.mestremagegn.icons.hasOwnProperty(type) ?
                    window.checkoutConfig.payment.mestremagegn.icons[type]
                    : false;
            },
            getCcAvailableTypesValues: function () {

                return _.map(window.checkoutConfig.payment.mestremagegn.ccavailabletypes, function (value, key) {
                    return {
                        'value': key,
                        'type': value
                    };
                });
            },
            getCcYearsValues: function () {
                return _.map(window.checkoutConfig.payment.mestremagegn.years, function (value, key) {
                    return {
                        'value': key,
                        'year': value
                    };
                });
            },
            getCcMonthsValues: function () {
                return _.map(window.checkoutConfig.payment.mestremagegn.months, function (value, key) {
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
                return window.checkoutConfig.payment.mestremagegn.type_view_credit_card;
            }),
            getSaveCardctive: ko.computed(function () {
                return window.checkoutConfig.payment.mestremagegn.accepts_save_card;
            }),

            getCustomScript: function () {
                $('.cardmemory').on('click', function(){

                    var type_view_credit_card = window.checkoutConfig.payment.mestremagegn.type_view_credit_card;

                    if(type_view_credit_card == 1) {
                        $('#mestremagegn_fullname').val(this.getAttribute('name-card')).trigger('keyup');
                        $('#mestremagegn_cc_number').val(this.getAttribute('number-card')).trigger('keyup');
                        $('#mestremagegn_expiration').val(this.getAttribute('month-card')).trigger('change');
                        $('#mestremagegn_expiration_yr').val(this.getAttribute('year-card')).trigger('change');
                    }else if(type_view_credit_card == 2){
                        $('#mestremagegn_cc_number').val(this.getAttribute('number-card')).trigger('keyup');
                        $('#mestremagegn_fullname').val(this.getAttribute('name-card')).trigger('keyup');
                        $('#mestremagegn_mm_yy').val(this.getAttribute('month-card')+' / '+this.getAttribute('year-card')).trigger('keyup');

                        $('#mestremagegn_cc_cid').focus();

                        $(".jp-card-container").hide();
                    }

                });

                $('.btndeletedhistorycard').on('click', function(){
                    var objdelhustory =  $('#mestremagegn_deletehistorycard');
                    objdelhustory.val(objdelhustory.val()+this.getAttribute('id-card')+',');
                    $('li#history-'+this.getAttribute('id-card')).css("display","none");
                });

            },

            getInstall: function () {

                var valor = quote.totals().base_grand_total;
                //console.log(valor);
                var type_interest 	= window.checkoutConfig.payment.mestremagegn.type_interest;
                var info_interest 	= window.checkoutConfig.payment.mestremagegn.info_interest;
                var min_installment = window.checkoutConfig.payment.mestremagegn.min_installment;
                var max_installment = window.checkoutConfig.payment.mestremagegn.max_installment;


                var json_parcelas = {};
                var count = 0;
                json_parcelas[1] =
                {'parcela' : priceUtils.formatPrice(valor, quote.getPriceFormat()),
                    'total_parcelado' : priceUtils.formatPrice(valor, quote.getPriceFormat()),
                    'total_juros' :  0,
                    'juros' : 0
                };

                if(min_installment == 0)
                    min_installment = 1;

                var max_div = (valor/min_installment);
                max_div = parseInt(max_div);


                if(max_div > max_installment) {
                    max_div = max_installment;
                }else{
                    if(max_div > 12) {
                        max_div = 12;
                    }
                }
                var limite = max_div;

                _.each( info_interest, function( key, value ) {
                    if(count <= max_div){
                        value = info_interest[value];
                        if(value > 0){

                            var taxa = value/100;
                            if(type_interest == "compound"){
                                var pw = Math.pow((1 / (1 + taxa)), count);
                                var parcela = ((valor * taxa) / (1 - pw));
                            } else {
                                var parcela = ((valor*taxa)+valor) / count;
                            }

                            var total_parcelado = parcela*count;

                            var juros = value;
                            if(parcela > 5 && parcela > min_installment){
                                json_parcelas[count] = {
                                    'parcela' : priceUtils.formatPrice(parcela, quote.getPriceFormat()),
                                    'total_parcelado': priceUtils.formatPrice(total_parcelado, quote.getPriceFormat()),
                                    'total_juros' : priceUtils.formatPrice(total_parcelado - valor, quote.getPriceFormat()),
                                    'juros' : juros,
                                };
                            }
                        } else {
                            if(valor > 0 && count > 0){
                                json_parcelas[count] = {
                                    'parcela' : priceUtils.formatPrice((valor/count), quote.getPriceFormat()),
                                    'total_parcelado': priceUtils.formatPrice(valor, quote.getPriceFormat()),
                                    'total_juros' :  0,
                                    'juros' : 0,
                                };
                            }
                        }
                    }
                    count++;
                });

                _.each( json_parcelas, function( key, value ) {
                    if(key > limite){
                        delete json_parcelas[key];
                    }
                });
                return json_parcelas;
            },

            getInstallments: function () {

                // og(this.getInstall());
                var temp = _.map(this.getInstall(), function (value, key) {
                    var inst = key+' x   '+ value['parcela']+'          total : ' + value['total_parcelado'];
                    return {
                        'value': key,
                        'installments': inst
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
                var acceptsSave = 0,cc_exp_month,cc_exp_year,cc_cid,cc_type,cc_number,x;
                if($("#accepts_save_card").is(":checked")) {
                    acceptsSave = 1;
                }

                if(this.getTypeViewCreditCard() == 2){
                    x = $('#mestremagegn_mm_yy').val();
                    x = x.replace(/\s/g,'');
                    array = x.split('/');
                    cc_exp_month = array[0];
                    cc_exp_year = array[1];

                    cc_cid = $('#mestremagegn_cc_cid').val();
                    cc_type = this.creditCardType();
                    cc_number =  $('#mestremagegn_cc_number').val().replace(/\s/g,'');

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
                        'accepts_save_card': acceptsSave,
                        'installments': jQuery('#'+this.getCode()+'_installments').val(),
                        'hash': jQuery('#'+this.getCode()+'_hash').val(),
                        'deletehistorycard': jQuery('#'+this.getCode()+'_deletehistorycard').val()
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
