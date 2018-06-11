<?php

namespace Unific\Extension\Block\Adminhtml;

class Request extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Unific\Extension\Model\RequestFactory
     */
    protected $_requestFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Unific\Extension\Model\RequestFactory $typeFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Unific\Extension\Model\RequestFactory $requestFactory,
        array $data = []
    ) {
        $this->_requestFactory = $requestFactory;

        parent::__construct($context, $data);
    }

    /**
     * Prepare button and grid
     *
     * @return \Unific\Extension\Block\Adminhtml\Request
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_new_request',
            'label' => __('Add Request'),
            'class' => 'add',
            'href' => $this->getUrl('extension/request/create', array('group' => $this->getRequest()->getParam('group'))),
            'button_class' => '',
            'class_name' => 'Magento\Backend\Block\Widget\Button',
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }
}
