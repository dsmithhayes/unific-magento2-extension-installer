<?php

namespace Unific\Extension\Model\Api\Data;

class SetupResponse implements \Unific\Extension\Api\Data\SetupResponseInterface
{
    /**
     * @var
     */
    protected $hmac;

    /**
     * @var
     */
    protected $totals;

    /**
     * @return \Unific\Extension\Api\Data\HmacInterface
     */
    public function getHmac()
    {
        return $this->hmac;
    }

    /**
     * @param \Unific\Extension\Api\Data\HmacInterface $hmac
     */
    public function setHmac(\Unific\Extension\Api\Data\HmacInterface $hmac)
    {
        $this->hmac = $hmac;
    }

    /**
     * @return \Unific\Extension\Api\Data\TotalsInterface
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * @param \Unific\Extension\Api\Data\TotalsInterface $totals
     */
    public function setTotals(\Unific\Extension\Api\Data\TotalsInterface $totals)
    {
        $this->totals = $totals;
    }


}