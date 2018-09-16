<?php

namespace Unific\Extension\Controller\Request;
class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('/');
        return $resultRedirect;
    }
}