<?php

namespace Unific\Extension\Model;

use Magento\Framework\Model\AbstractModel;

class Request extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Unific\Extension\Model\ResourceModel\Request');
    }
}