define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/url'
], function (
    $,
    ko,
    Component,
    fullScreenLoader,
    urlBuilder
) {
    return {
        ajaxSave: function () {
            var createNewAccountCheckBoxId = 'create-new-customer',
                loginFormSelector = 'form[data-role=email-with-possible-login]';
            var data = {};
            if ($(loginFormSelector + ' #' + createNewAccountCheckBoxId).is(":checked")) {
                data['email'] = $(loginFormSelector + ' input[name=username]').val();
                data['pass'] = $(loginFormSelector + ' input[name=newcustomerpassword]').val();
                data['confirmpass'] = $(loginFormSelector + ' input[name=newcustomerpassword_confirmation]').val();
            }
            var saveUrl = 'onepagecheckout/account/save';
            fullScreenLoader.startLoader();
            $.ajax({
                url: urlBuilder.build(saveUrl),
                data: data,
                type: 'post',
                dataType: 'json',

                /** @inheritdoc */
                success: function (response) {
                    fullScreenLoader.stopLoader();
                },

                /** @inheritdoc */
                error: function () {
                    alert({
                        content: $.mage.__('Sorry, something went wrong. Please try again later.')
                    });
                }
            });
        }
    };
});
