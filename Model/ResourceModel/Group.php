<?php

namespace Unific\Extension\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Group extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('unific_extension_group', 'id');
    }
}