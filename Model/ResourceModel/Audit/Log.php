<?php

namespace Unific\Extension\Model\ResourceModel\Audit;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('unific_extension_audit_log', 'id');
    }
}