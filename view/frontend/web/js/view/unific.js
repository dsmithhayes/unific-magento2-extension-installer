/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/template'
], function ($, _, mageTemplate) {
    'use strict';
    return function(config, element) {
        console.log('Unific JS Loaded');

        var $formElement = ('<input type="hidden" name="shippingAddress.email" id="shipping-email" />');
        $('#co-shipping-form').prepend($formElement);
    }
});