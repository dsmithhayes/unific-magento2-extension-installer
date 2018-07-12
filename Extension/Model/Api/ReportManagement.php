<?php

namespace Unific\Extension\Model\Api;

use Unific\Extension\Api\ReportManagementInterface;

class ReportManagement implements ReportManagementInterface
{
    protected $scopeConfig;

    protected $orderCollectionFactory;
    protected $categoryCollectionFactory;
    protected $customerCollectionFactory;
    protected $productCollectionFactory;

    /**
     * ModeManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory

    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Get the total amount of categories
     *
     * @return string
     */
    public function getCategoryCount()
    {
        return $this->categoryCollectionFactory->create()->getSize();
    }

    /**
     * Get the total amount of customers
     *
     * @return string
     */
    public function getCustomerCount()
    {
        return $this->customerCollectionFactory->create()->getSize();
    }

    /**
     * Get the total amount of products
     *
     * @return string
     */
    public function getProductCount()
    {
        return $this->productCollectionFactory->create()->getSize();
    }

    /**
     * Get the total amount of orders
     *
     * @return string
     */
    public function getOrderCount()
    {
        return $this->orderCollectionFactory->create()->getSize();
    }
}