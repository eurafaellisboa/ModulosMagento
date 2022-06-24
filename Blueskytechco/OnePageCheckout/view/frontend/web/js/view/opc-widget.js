define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function (
        $,
        ko,
        Component
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Blueskytechco_OnePageCheckout/widget/opc-widget'
            },
            getOpcWidget: function (position) {
                var widgetList = window.checkoutConfig.opcWidget,
                    result = ko.observableArray([]);
                if (position == "widget_after_placeorder") {
                    $.each(widgetList.widget_after_placeorder, function (index, value) {
                        result.push(value);
                    });
                }
                return result;
            }
        });
    }
);
