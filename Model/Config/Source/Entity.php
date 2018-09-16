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
            ['value' => 'catalog_category', 'label' => 'Category'],
            ['value' => 'catalog_product_entity', 'label' => 'Product'],
            ['value' => 'customer', 'label' => 'Customer'],
            ['value' => 'customer_address', 'label' => 'Customer Address'],
            ['value' => 'order', 'label' => 'Order'],
            ['value' => 'invoice', 'label' => 'Invoice'],
            ['value' => 'shipment', 'label' => 'Shipment'],
            ['value' => 'creditmemo', 'label' => 'Credit Memo']
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
            'catalog_category' => 'Category',
            'catalog_product_entity' => 'Product',
            'customer' => 'Customer',
            'customer_address' => 'Customer Address',
            'order' => 'Order',
            'invoice' => 'Invoice',
            'shipment' => 'Shipment',
            'creditmemo' => 'Credit Memo',
        ];
    }
}
