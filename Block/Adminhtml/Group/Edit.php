<?php

// @codingStandardsIgnoreFile

namespace Unific\Extension\Block\Adminhtml\Group;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);

        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('reset');
    }

    /**
     * Initialize blog post edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Unific_Extension';
        $this->_controller = 'Adminhtml_Group';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Group'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Group'));
    }

    /**
     * Retrieve server object
     *
     * @return \Unific\Extension\Model\Server
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_extension_group');
    }

    /**
     * Return header text for form
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $serverRegistry = $this->_coreRegistry->registry('_extension_group');
        if ($serverRegistry->getId()) {
            $newsTitle = $this->escapeHtml($serverRegistry->getTitle());
            return __("Edit Group '%1'", $newsTitle);
        } else {
            return __('Add Group');
        }
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('post_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            };
        ";

        return parent::_prepareLayout();
    }
}