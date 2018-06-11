<?php

namespace Unific\Extension\Controller\Adminhtml\Request;

class Delete extends \Unific\Extension\Controller\Adminhtml\Request
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->_objectManager->create('Unific\Extension\Model\Request');
            $model->load($id);


            // Check this news exists or not
            if (!$model->getId()) {
                $this->messageManager->addError(__('This request no longer exists.'));
            } else {
                try {
                    // Delete news
                    $model->delete();
                    $this->messageManager->addSuccess(__('The request has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {

                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $model->getId()]);
                }
            }
        }
    }
}