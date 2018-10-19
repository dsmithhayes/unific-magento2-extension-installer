/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'jquery',
    'mage/storage',
    'Magento_Customer/js/customer-data'
], function (ko, $, storage, customerData) {
    'use strict';

    $.widget('unific.js', {
       _create: function() {
           console.log('Unific JS Loaded');
           var checkoutData = customerData.get('checkout-data');

           customerData.get('checkout-data').subscribe(function(newValue)
           {
               if(checkoutData.inputFieldEmailValue) {
                   console.log('email configured to: ' + checkoutData.inputFieldEmailValue);
               }

               console.log('Cart changed');
               console.log(newValue);
           });
       }
    });

    return $.unific.js;
});
