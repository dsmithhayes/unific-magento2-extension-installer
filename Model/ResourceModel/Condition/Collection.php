<?php

namespace Unific\Extension\Model\ResourceModel\Condition;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Unific\Extension\Model\Condition',
            'Unific\Extension\Model\ResourceModel\Condition');
    }
}