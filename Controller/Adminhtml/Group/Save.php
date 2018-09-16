<?php

namespace Unific\Extension\Controller\Adminhtml\Group;

use Unific\Extension\Controller\Adminhtml\Group;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends Group
{
    /**
     * Save Group
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/group'));
        }

        $group = $this->_objectManager->create('Unific\Extension\Model\Group');
        $id = (int)$request->getParam('id');

        if ($id) {
            $group->load($id);
        }

        try {
            $data = $request->getParams();

            $group->addData($data);
            $group->save();

            $this->messageManager->addSuccess(__('The group has been saved.'));
            $this->_getSession()->setFormData(false);

        } catch (LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('extension_group_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['id' => $group->getId(), '_current' => true]);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving this group.'));
            $this->_getSession()->setData('extension_group_form_data', $this->getRequest()->getParams());
            return $resultRedirect->setPath('*/*/edit', ['id' => $group->getId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
