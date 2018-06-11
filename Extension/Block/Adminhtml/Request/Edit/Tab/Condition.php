<?php

namespace Unific\Extension\Block\Adminhtml\Request\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Condition extends Generic
{
    protected $_template = 'Unific_Extension::condition.phtml';

    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_extension_request');
    }

    public function getWebsites()
    {
        $websites = array();
        foreach($this->_storeManager->getWebsites() as $website) {
            $websites[] = array('id' => $website->getId(), 'name' => $website->getName());
        }

        return $websites;
    }

    public function getStores()
    {
        $stores = array();

        foreach($this->_storeManager->getStores() as $store) {
            $stores[] = array('id' => $store->getId(), 'name' => $store->getName());
        }

        return $stores;
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form_info', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}