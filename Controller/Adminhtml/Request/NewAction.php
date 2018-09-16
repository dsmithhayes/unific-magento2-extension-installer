<?php

namespace Unific\Extension\Controller\Adminhtml\Request;

use Unific\Extension\Controller\Adminhtml\Request;

class NewAction extends Request
{
    public function execute()
    {
        $this->_forward('edit');
    }
}