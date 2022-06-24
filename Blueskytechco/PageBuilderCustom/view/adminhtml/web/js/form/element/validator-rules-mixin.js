define([
    'jquery',
    'underscore',
    'Magento_Ui/js/lib/validation/utils'
], function ($, _, utils) {
    'use strict';

    function validateCategoryThumbnailImage(value) {
        var appea = $('.admin__field-visual-select input[name="appearance"]').val();
        var selectTemp = $('.admin__field-visual-select input[name="template_id"]').val();
        var selectedItem = value.length;
        if(appea == 'grid'){
            if(selectTemp == 'default'){
                if(selectedItem > 4){
                    return false;
                }
            }
            else if(selectTemp == 'default_2'){
                if(selectedItem > 3){
                    return false;
                }
            }
            else if(selectTemp == 'default_3'){
                if(selectedItem > 4){
                    return false;
                }
            }
            else if(selectTemp == 'default_4'){
                if(selectedItem > 5){
                    return false;
                }
            }
            else if(selectTemp == 'default_5'){
                if(selectedItem > 5){
                    return false;
                }
            }
            else if(selectTemp == 'default_6'){
                if(selectedItem > 5){
                    return false;
                }
            }
        }

        return true;
    }

    return function (validator) {

        validator.addRule(
            'validate-category-thumbnail-image',
            function (value) {
                return validateCategoryThumbnailImage(value);
            },
            $.mage.__('Please select valid Limited categories.')
        );

        return validator;
    };
});