<?php

namespace Unific\Extension\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

use Unific\Extension\Model\ResourceModel\ResponseCondition\CollectionFactory;

class Response implements ArrayInterface
{
    /**
     * @var array
     */
    protected $options;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->collectionFactory->create()->toOptionIdArray();
        }
        return $this->options;
    }
}