<?php

namespace Unific\Extension\Model\ResourceModel\Audit\Log;

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
        $this->_init('Unific\Extension\Model\Audit\Log',
            'Unific\Extension\Model\ResourceModel\Audit\Log');
    }
}