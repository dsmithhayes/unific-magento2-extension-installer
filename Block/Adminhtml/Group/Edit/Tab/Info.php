<?php

namespace Unific\Extension\Block\Adminhtml\Group\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Info extends Generic
{
    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_extension_group');
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

        $form->setFieldContainerIdPrefix('info_');
        $form->addFieldNameSuffix('info');

        $fieldset = $form->addFieldset('base_fieldset',
            ['legend' => __('Group Information'), 'class' => 'fieldset-wide']);

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }


        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'description',
                'label' => __('Group Description'),
                'title' => __('Group Description'),
                'required' => false,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}