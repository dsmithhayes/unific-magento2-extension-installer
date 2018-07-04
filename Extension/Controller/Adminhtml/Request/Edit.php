<?php

namespace Unific\Extension\Controller\Adminhtml\Request;

class Edit extends \Unific\Extension\Controller\Adminhtml\Request
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $model = $this->_objectManager->create('Unific\Extension\Model\Request');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        $this->_coreRegistry->register('_extension_request', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Unific_Extension::request');
        $resultPage->getConfig()->getTitle()->prepend(__('Request'));

        return $resultPage;
    }
}
