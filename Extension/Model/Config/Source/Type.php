<?php
/**
 * Used in creating options for Server Type config value selection
 *
 */
namespace Unific\Extension\Model\Config\Source;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'rest', 'label' => 'REST'], ['value' => 'soap', 'label' => 'SOAPv2']];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['rest' => 'REST', 'soap' => 'SOAPv2'];
    }
}
