<?php

namespace Unific\Extension\Controller\Adminhtml\Group;

use Unific\Extension\Controller\Adminhtml\Group;

class NewAction extends Group
{
    public function execute()
    {
        $this->_forward('edit');
    }
}