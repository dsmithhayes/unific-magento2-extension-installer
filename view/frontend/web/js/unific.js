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

           // If the email is set, we need to send the cart data
           customerData.get('checkout-data').subscribe(function(newValue)
           {
               if(newValue.inputFieldEmailValue && this.isEmail(newValue.inputFieldEmailValue)) {
                   console.log('email configured to: ' + newValue.inputFieldEmailValue);
               }
           });

           // If the cart data is updated, we need to update the cart data
           customerData.get('cart').subscribe(function(newValue)
           {
               var checkoutData = customerData.get('checkout-data');
               console.log(newValue);

               if(checkoutData.inputFieldEmailValue) {
                   console.log('Sending cart update for: ' + checkoutData.inputFieldEmailValue);
               }
           });
       },

        isEmail: function(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }
    });

    return $.unific.js;
});
