<?php
/**
 * Setting the operation mode of the Unific extension
 *
 */
namespace Unific\Extension\Model\Config\Source;

class Entity implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'category', 'label' => 'Category'],
            ['value' => 'product', 'label' => 'Product'],
            ['value' => 'customer', 'label' => 'Customer'],
            ['value' => 'customer_billing_address', 'label' => 'Billing Address'],
            ['value' => 'customer_shipment_address', 'label' => 'Shipment Address'],
            ['value' => 'order', 'label' => 'Order'],
            ['value' => 'invoice', 'label' => 'Invoice']
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'category' => 'Category',
            'product' => 'Product',
            'customer' => 'Customer',
            'customer_billing_address' => 'Billing Address',
            'customer_shipment_address' => 'Shipment Address',
            'order' => 'Order',
            'invoice' => 'Invoice',
        ];
    }
}
