// if no dependencies, the first argument is not required
define(['underscore', "jquery"], function(_, $) {
    return function(config, element) {
        console.log('Unific JS Loaded');
        $('div[name="shippingAddress.firstname"]').before('<input type="hidden" name="shippingAddress.email" />');

        $('input#customer-email').keyup(function() {
            $('input[name="shippingAddress.email"]').val($('input#customer-email').val());
        });
    }
});