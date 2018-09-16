<?php

namespace Unific\Extension\Block\Adminhtml\Request\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('server_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Request Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {

        $this->addTab(
            'request_info',
            [
                'label' => __('Configuration'),
                'title' => __('Configuration'),
                'content' => $this->getLayout()->createBlock(
                    'Unific\Extension\Block\Adminhtml\Request\Edit\Tab\Info'
                )->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'request_condition',
            [
                'label' => __('Webhook Conditions'),
                'title' => __('Webhook Conditions'),
                'content' => $this->getLayout()->createBlock(
                    'Unific\Extension\Block\Adminhtml\Request\Edit\Tab\Condition'
                )->toHtml(),
                'active' => false
            ]
        );

        $this->addTab(
            'request_mapping',
            [
                'label' => __('Webhook Mappings'),
                'title' => __('Webhook Mappings'),
                'content' => $this->getLayout()->createBlock(
                    'Unific\Extension\Block\Adminhtml\Request\Edit\Tab\Mapping'
                )->toHtml(),
                'active' => false
            ]
        );

        $this->addTab(
            'response_info',
            [
                'label' => __('Response Conditions'),
                'title' => __('Response Conditions'),
                'content' => $this->getLayout()->createBlock(
                    'Unific\Extension\Block\Adminhtml\Request\Edit\Tab\Response'
                )->toHtml(),
                'active' => false
            ]
        );

        $this->addTab(
            'response_mapping',
            [
                'label' => __('Response Mappings'),
                'title' => __('Response Mappings'),
                'content' => $this->getLayout()->createBlock(
                    'Unific\Extension\Block\Adminhtml\Request\Edit\Tab\ResponseMapping'
                )->toHtml(),
                'active' => false
            ]
        );

        return parent::_beforeToHtml();
    }
}