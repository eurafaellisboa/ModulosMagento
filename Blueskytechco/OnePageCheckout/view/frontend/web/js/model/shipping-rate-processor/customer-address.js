define([
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/error-processor'
], function (resourceUrlManager, quote, storage, shippingService, rateRegistry, errorProcessor) {
    'use strict';

    return function (address) {
        shippingService.isLoading(true);
        return storage.post(
            resourceUrlManager.getUrlForEstimationShippingMethodsByAddressId(),
            JSON.stringify({
                addressId: address.customerAddressId
            }),
            false
        ).done(function (result) {
            console.log('register');
            rateRegistry.set(address.getKey(), result);
            shippingService.setShippingRates(result);
        }).fail(function (response) {

            console.log('cccc');
            shippingService.setShippingRates([]);
            errorProcessor.process(response);
        }).always(function () {

            console.log('ddd');
                shippingService.isLoading(false);
            }
        );
    }
});
