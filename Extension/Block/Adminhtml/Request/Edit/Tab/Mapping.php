<?php

namespace Unific\Extension\Block\Adminhtml\Request\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Mapping extends Generic
{
    protected $_template = 'Unific_Extension::mapping.phtml';

    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_extension_request');
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
            ['data' => ['id' => 'edit_form_request_mapping', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('request_mapping_');
        $form->addFieldNameSuffix('request_mapping');

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getEavAttributes()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $eavModel = $objectManager->get('\Magento\Eav\Model\Config');

        return array(
            'customer' => array_merge_recursive(array('ip'), $eavModel->getEntityAttributeCodes('customer')),
            'customer_address' => $eavModel->getEntityAttributeCodes('customer_address'),
            'catalog_category' => $eavModel->getEntityAttributeCodes('catalog_category'),
            'catalog_product' => $eavModel->getEntityAttributeCodes('catalog_product'),
            'other' => array()
        );
    }
}