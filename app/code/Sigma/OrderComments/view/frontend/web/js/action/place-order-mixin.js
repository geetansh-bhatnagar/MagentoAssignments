define([
    'jquery',
    'mage/utils/wrapper',
    'Sigma_OrderComments/js/action/shipping-comment-processor'
], function ($, wrapper, shippingCommentProcessor) {
    'use strict';

    return function (placeOrderAction) {

        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            shippingCommentProcessor(paymentData);

            return originalAction(paymentData, messageContainer);
        });
    };
});
