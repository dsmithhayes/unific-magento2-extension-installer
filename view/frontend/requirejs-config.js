var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': {
                'Unific_Extension/js/model/shipping-rate-processor/new-address-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rate-processor/customer-address': {
                'Unific_Extension/js/model/shipping-rate-processor/customer-address-mixin': true
            }
        }
    }
};