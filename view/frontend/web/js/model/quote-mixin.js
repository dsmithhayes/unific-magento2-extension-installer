/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @api
 */
define([
    'ko',
    'underscore'
], function (ko, _) {
    'use strict';

    return function (quote) {
        quote.shippingAddress['email'] = $('#customer-email').val();
        quote.billingAddress['email'] = $('#customer-email').val();

        return quote;
    };
});