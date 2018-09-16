<?php

namespace Unific\Extension\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface SetupResponseInterface extends ExtensibleDataInterface
{
    /**
     * @return \Unific\Extension\Api\Data\HmacInterface
     */
    public function getHmac();

    /**
     * @param \Unific\Extension\Api\Data\HmacInterface
     * @return void
     */
    public function setHmac(\Unific\Extension\Api\Data\HmacInterface $hmacInterface);

    /**
     * @return \Unific\Extension\Api\Data\TotalsInterface
     */
    public function getTotals();

    /**
     * @param TotalsInterface $totalsInterface
     * @return void
     */
    public function setTotals(\Unific\Extension\Api\Data\TotalsInterface $totalsInterface);
}