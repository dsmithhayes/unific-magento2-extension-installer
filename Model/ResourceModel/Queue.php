<?php

namespace Unific\Extension\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Queue extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('unific_extension_message_queue', 'guid');
        $this->_isPkAutoIncrement = false;
    }
}