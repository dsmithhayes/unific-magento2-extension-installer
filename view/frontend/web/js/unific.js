/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/storage',
    'Magento_Customer/js/customer-data'
], function ($, storage, customerData) {
    'use strict';

    $.widget('unific.js', {
       _create: function() {
           console.log('Unific JS Loaded');

           if(checkoutData.inputFieldEmailValue) {
               console.log('email configured to: ' + checkoutData.inputFieldEmailValue);
           }

           if(customerData.get('unific-cart') != customerData.get('checkout-data'))
           {
               customerData.set('unific-cart', customerData.get('checkout-data'));
               console.log('Cart changed');
               console.log(customerData.get('unific-cart'));
           }
       }
    });

    return $.unific.js;
});
