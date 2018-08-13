<?php

namespace Unific\Extension\Plugin;

class CustomerSessionPlugin
{
    protected $requestCollection;

    protected $logger;

    public function __construct(
        \Unific\Extension\Model\ResourceModel\Request\Grid\Collection $requestCollection,
        \Unific\Extension\Logger\Logger $logger
    )
    {
        $this->requestCollection = $requestCollection;

        $this->logger = $logger;
    }

    public function beforeSetCustomerAsLoggedIn($model)
    {
    }

    public function afterSetCustomerAsLoggedIn($model)
    {
    }

    public function beforeLogout($model)
    {
    }

    public function afterLogout($model)
    {
    }
}
