<?php

namespace Unific\Extension\Controller\Adminhtml\Group;

class Edit extends \Unific\Extension\Controller\Adminhtml\Group
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $model = $this->_objectManager->create('Unific\Extension\Model\Group');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }
        
        $this->_coreRegistry->register('_extension_group', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Unific_Extension::group');
        $resultPage->getConfig()->getTitle()->prepend(__('Group'));

        return $resultPage;
    }
}