<?php

namespace Unific\Extension\Block\Adminhtml\Request\Edit\Tab;

class ResponseMapping extends Mapping
{
    protected $_template = 'Unific_Extension::response-mapping.phtml';

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
            ['data' => ['id' => 'edit_form_response_mapping', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('response_mapping_');
        $form->addFieldNameSuffix('response_mapping');

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}