// if no dependencies, the first argument is not required
define(['underscore', "jquery"], function(_, $) {
    return function(config, element) {
        console.log('Unific JS Loaded');
        $(element).prepend('<input type="hidden" name="shippingAddress.email" id="shipping-email" />');

        $('#customer-email').keyup(function() {
            $('#shipping-email').val($('#customer-email').val());
        });
    }
});