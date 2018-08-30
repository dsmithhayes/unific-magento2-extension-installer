<?php

namespace Unific\Extension\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface TotalsInterface extends ExtensibleDataInterface
{
    public function getCategory();
    public function setCategory($categoryCount = 0);

    public function getProduct();
    public function setProduct($productCount = 0);

    public function getOrder();
    public function setOrder($orderCount = 0);

    public function getCustomer();
    public function setCustomer($customerCount = 0);
}