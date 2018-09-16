<?php

namespace Unific\Extension\Controller\Adminhtml\Group;

class Delete extends \Unific\Extension\Controller\Adminhtml\Group
{
    /**
     * @return void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->_objectManager->create('Unific\Extension\Model\Group');
            $model->load($id);


            // Check this news exists or not
            if (!$model->getId()) {
                $this->messageManager->addError(__('This group no longer exists.'));
            } else {
                try {
                    // Delete news
                    $model->delete();
                    $this->messageManager->addSuccess(__('The group has been deleted.'));

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