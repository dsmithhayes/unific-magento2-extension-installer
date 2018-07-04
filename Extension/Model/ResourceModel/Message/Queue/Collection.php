<?php

namespace Unific\Extension\Model\ResourceModel\Message\Queue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'guid';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Unific\Extension\Model\Message\Queue',
            'Unific\Extension\Model\ResourceModel\Message\Queue');
    }
}