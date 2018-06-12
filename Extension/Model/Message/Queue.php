<?php

namespace Unific\Extension\Model\Message;

use Magento\Framework\Model\AbstractModel;

class Queue extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Unific\Extension\Model\ResourceModel\Message\Queue');
    }
}