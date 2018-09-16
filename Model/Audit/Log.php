<?php

namespace Unific\Extension\Model\Audit;

use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Unific\Extension\Model\ResourceModel\Audit\Log');
    }
}