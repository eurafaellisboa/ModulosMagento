define([
    'jquery',
    'MGS_AjaxCart/js/config',
    'MGS_AjaxCart/js/action',
], function($, mgsConfig) {
    "use strict";
    jQuery.widget('mgs.catalogAddToCart', jQuery.mgs.action, {
        options: {
            bindSubmit: true,
            redirectToCatalog: false
        },
        _create: function() {
            if (this.options.bindSubmit) {
                this._super();
                this._on({
                    'submit': function(event) {
                        event.preventDefault();

                        var data = this.element.serializeArray();
                        var formData = new FormData();
                        for(var i = 0; i < data.length; i++){
                            formData.append(data[i].name, data[i].value);
                        }
                        var files = $('input[type="file"]');
                        files.each(function(index){
                            formData.append(files[index].name, files[index].files[0]);
                        });
                        formData.append('action_url', this.element.attr('action'));
                        this.fire(this.element,this.getActionId(), this.element.attr('action'), formData, this.options.redirectToCatalog);
                    }
                });
            }
            
        },
        getActionId: function() {
            return 'catalog-add-to-cart-' + jQuery.now()
        }
    });

    return jQuery.mgs.catalogAddToCart;
});
