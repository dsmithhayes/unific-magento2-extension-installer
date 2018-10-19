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

    $.widget('mage.unific', {
       _create: function() {
           console.log('Unific JS Loaded');

           var checkoutData = customerData.get('checkout-data')();

           if(checkoutData.inputFieldEmailValue) {
               console.log('email configured to: ' + checkoutData.inputFieldEmailValue);
           }
       }
    });
});
