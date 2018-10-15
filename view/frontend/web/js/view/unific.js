// if no dependencies, the first argument is not required
define(['underscore', "jquery"], function(_, $) {
    return function(config, element) {
        console.log('Unific JS Loaded');
        $('[name=shippingAddress.firstname').before('<input type="hidden" name="shippingAddress.email" />');

        $('#customer-email').onkeyup(function() {
            $('[name=shippingAddress.email]').val($('#customer-email').val());
        });
    }
});