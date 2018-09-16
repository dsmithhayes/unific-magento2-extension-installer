<?php

namespace Unific\Extension\Controller\Adminhtml\Group;

use Magento\Framework\Controller\ResultFactory;
use Unific\Extension\Controller\Adminhtml\Group;

class Index extends Group
{
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Groups'));
        return $resultPage;
    }
}
