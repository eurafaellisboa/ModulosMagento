define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Blueskytechco_OnePageCheckout/js/model/resource-url-manager',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/full-screen-loader',
        'uiRegistry'
    ],
    function ($,
              quote,
              resourceUrlManager,
              storage,
              errorProcessor,
              customer,
              methodConverter,
              paymentService,
              shippingService,
              opcLoader,
              registry) {
        'use strict';

        return function () {
            opcLoader.startLoader();

            return storage.post(
                resourceUrlManager.getUrlForUpdatePaymentTotalInformation(quote)
            ).done(
                function (response) {
                    var options, paths;

                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                        return;
                    }

                    // remove downloadable options on cart item reload
                    $('#downloadable-links-list').remove();
                    $('#links-advice-container').remove();

                    if (response.image_data) {
                        registry.get('checkout.sidebar.summary.cart_items.details.thumbnail').imageData
                            = JSON.parse(response.image_data);
                    }

                    if (response.options) {
                        options = JSON.parse(response.options);

                        response.totals.items.forEach(function (item) {
                            item.rkopc = options[item.item_id];
                        });
                    }

                    if (response.request_path) {
                        paths = JSON.parse(response.request_path);

                        response.totals.items.forEach(function (item) {
                            item.request_path = paths[item.item_id];
                        });
                    }

                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            ).always(
                function () {
                    opcLoader.stopLoader();
                }
            );
        };
    }
);
