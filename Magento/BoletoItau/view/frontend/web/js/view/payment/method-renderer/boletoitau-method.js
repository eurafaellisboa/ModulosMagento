define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_BoletoItau/payment/boletoitau'
            },
            getCode: function() {
                return 'boletoitau';
            },
            getData: function () {
                return {
                    'method': this.item.method,
                    'additional_data': {
                        'boletoitau_taxvat': jQuery('#'+this.getCode()+'_cpf').val()
                    }
                };
            },
            mask: function() {
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

            }

        });
    }
);
