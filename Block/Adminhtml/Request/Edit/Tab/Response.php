<?php
/**
 * Created by PhpStorm.
 * User: ron
 * Date: 3-10-16
 * Time: 13:58
 */

namespace Unific\Extension\Block\Adminhtml\Request\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Response extends Generic
{
    protected $_template = 'Unific_Extension::response-condition.phtml';

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
            ['data' => ['id' => 'edit_form_response', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('response_');
        $form->addFieldNameSuffix('response');

        $fieldset = $form->addFieldset('base_fieldset',
            ['legend' => __('Response Information'), 'class' => 'fieldset-wide']);

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'readonly' => true
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Response Description'),
                'title' => __('Response Description'),
                'required' => false,
                'readonly' => true
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}