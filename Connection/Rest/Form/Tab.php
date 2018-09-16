<?php

namespace Unific\Extension\Connection\Rest\Form;

use Magento\Backend\Block\Widget\Form\Generic;

class Tab extends Generic
{
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
            ['data' => ['id' => 'edit_form_rest', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('rest_');
        $form->addFieldNameSuffix('rest');

        $fieldset = $form->addFieldset('base_fieldset',
            ['legend' => __('Rest Settings'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'request_url',
            'text',
            [
                'name' => 'request_url',
                'label' => __('Request URL'),
                'title' => __('Request URL'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'request_method',
            'select',
            [
                'name' => 'request_method',
                'label' => __('Request Method'),
                'title' => __('Request Method'),
                'required' => true,
                'values' => array(
                    'post' => 'POST',
                    'get' => 'GET',
                    'put' => 'PUT',
                    'delete' => 'DELETE'
                )
            ]
        );

        $form->setValues($model->getData('type_rest'));
        $this->setForm($form);

        return parent::_prepareForm();
    }
}