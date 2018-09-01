<?php

namespace Unific\Extension\Block\Adminhtml\Request\Edit\Tab;

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
            ['data' => ['id' => 'edit_form_info', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('info_');
        $form->addFieldNameSuffix('info');

        $fieldset = $form->addFieldset('base_fieldset',
            ['legend' => __('Request Information'), 'class' => 'fieldset-wide']);

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }

        $fieldset->addField(
            'group_id',
            'hidden',
            [
                'name' => 'group_id'
            ]
        );

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
            'unique_id',
            'text',
            [
                'name' => 'unique_id',
                'label' => __('Unique ID'),
                'title' => __('Unique ID'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Request Description'),
                'title' => __('Request Description'),
                'required' => false,
            ]
        );

        $fieldset->addField(
            'request_event_execution',
            'select',
            [
                'name' => 'request_event_execution',
                'label' => __('Event Execution'),
                'title' => __('Event Execution'),
                'values' => array(
                    'before' => 'Before',
                    'after' => 'After'
                )
            ]
        );

        $fieldset->addField(
            'request_event',
            'select',
            [
                'name' => 'request_event',
                'label' => __('Called Event'),
                'title' => __('Called Event'),
                'class' => 'required-entry',
                'required' => true,
                'onchange' => 'jQuery(\'.request_event_info\').parent().parent().hide();if(this.value.indexOf(\'::\') < 0) { jQuery(\'.request_event_info_\' + this.value).parent().parent().show(); }',
                'onload' => 'jQuery(this).trigger(\'change\')',
                'values' => array(
                    'Magento\Customer\Model\Session::setCustomerAsLoggedIn' => 'Customer logs in',
                    'Magento\Customer\Model\Session::logout' => 'Customer logs out',
                    'Magento\Backend\Model\Auth\Session::processLogin' => 'Admin user logs in',
                    'Magento\Backend\Model\Auth\Session::processLogout' => 'Admin user logs out',
                    'Magento\Customer\Api\AccountManagementInterface::createAccount' => 'Customer creates account' ,
                    'Magento\User\Model\User::save' => 'Admin user is saved',
                    'Magento\Quote\Api\CartManagementInterface::save' => 'Cart is saved',
                    'Magento\Sales\Api\OrderManagementInterface::place' => 'Order is placed',
                    'Magento\Sales\Api\Order::save' => 'Order is updated',
                    'Magento\Sales\Model\Order\Invoice::capture' => 'Invoice is captured',
                    'Magento\Sales\Model\Order\Creditmemo::save' => 'Refund is created',
                    'Magento\Shipment\Model\Shipment::save' => 'Shipment is created',
                    'Magento\Catalog\Model\Category::save' => 'Category is saved',
                    'Magento\Catalog\Api\ProductManagementInterface::save' => 'Product is saved',
                )
            ]
        );

        $fieldset->addField(
            'request_event_url',
            'text',
            [
                'name' => 'request_event_url',
                'label' => __('Request URL'),
                'title' => __('Request URL'),
                'class' => 'request_event_info request_event_info_url',
                'required' => false,
                'placeholder' => '/extension/request/index'
            ]
        );

        $fieldset->addField(
            'request_event_other_class',
            'text',
            [
                'name' => 'request_event_other_class',
                'label' => __('Event Class'),
                'title' => __('Event Class'),
                'class' => 'request_event_info request_event_info_other',
                'required' => false,
                'placeholder' => 'Magento\Customer\Model\Session'
            ]
        );

        $fieldset->addField(
            'request_event_other_method',
            'text',
            [
                'name' => 'request_event_other_method',
                'label' => __('Event Class Method'),
                'title' => __('Event Class Method'),
                'class' => 'request_event_info request_event_info_other',
                'required' => false,
                'placeholder' => 'setCustomerAsLoggedIn'
            ]
        );

        $form->addValues(array('group_id' => $this->getRequest()->getParam('group')));
        $form->addValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
