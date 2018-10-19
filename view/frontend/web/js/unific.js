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

           var checkoutData = customerData.get('checkout-data')();
           var cartData = customerData.get('cart');
           cartData.watch('p', function (id, oldval, newval) {
              console.log('Cart changed');
              console.log(oldval);
              console.log(newval);
           });

           if(checkoutData.inputFieldEmailValue) {
               console.log('email configured to: ' + checkoutData.inputFieldEmailValue);


           }
       }
    });

    return $.unific.js;
});
