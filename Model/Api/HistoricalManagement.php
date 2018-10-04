<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\HistoricalManagementInterface;

class HistoricalManagement implements HistoricalManagementInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $historicalHelper;

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Unific\Extension\Helper\Historical $historicalHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Extension\Helper\Historical $historicalHelper
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->historicalHelper = $historicalHelper;
    }

    /**
     * Sets the mode
     *
     * @api
     *
     * @return bool true on success
     */
    public function setTrigger()
    {
        return $this->historicalHelper->queueAllHistoricalData();
    }
}