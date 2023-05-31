define([
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote'
    ], function (
        $, ko, Component, quote) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Sigma_OrderComments/checkout/shipping-comment-block'
            },
            initObservable: function () {
                this.selectedMethod = ko.computed(function() {
                    var method = quote.shippingMethod();
                    var selectedMethod = method != null ? method.method_code : null;
                    return selectedMethod;
                }, this);

                return this;
            },
        });
    }
);
