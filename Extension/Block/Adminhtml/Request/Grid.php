<?php

namespace Unific\Extension\Block\Adminhtml\Request;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Unific_Extension';
        $this->_controller = 'adminhtml_request';
        $this->_headerText = __('Requests');
        $this->_addButtonLabel = __('Add New Request');
        parent::_construct();
        $this->buttonList->add(
            'image_apply',
            [
                'label' => __('Image'),
                'onclick' => "location.href='" . $this->getUrl('configurablebundle/*/applyImage') . "'",
                'class' => 'apply'
            ]
        );
    }

    /**
     * Create "New" button
     *
     * @return void
     */
    protected function _addNewButton()
    {
        $this->addButton(
            'add',
            [
                'label' => $this->getAddButtonLabel(),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/new', array('group' => $this->getRequest()->getParam('group'))) . '\')',
                'class' => 'add primary'
            ]
        );
    }
}