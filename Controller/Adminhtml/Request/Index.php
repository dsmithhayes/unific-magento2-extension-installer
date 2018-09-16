<?php

namespace Unific\Extension\Controller\Adminhtml\Request;

use Magento\Framework\Controller\ResultFactory;
use Unific\Extension\Controller\Adminhtml\Request;

class Index extends Request
{
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Requests'));
        return $resultPage;
    }
}