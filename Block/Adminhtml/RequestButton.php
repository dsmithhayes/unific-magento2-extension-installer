<?php

namespace Unific\Extension\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class RequestButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    protected $request;

    /**
     * CustomButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [
            'label' => __('Add New Request'),
            'class' => 'save primary',
            'id' => 'custom-button',
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/new', ['group' => $this->request->getParam('id')])),
            'sort_order' => 20,
        ];
        return $data;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
