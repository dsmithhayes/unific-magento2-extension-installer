<?php

namespace Unific\Extension\Plugin;

class AdminSessionPlugin
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

    public function beforeProcessLogin($model)
    {

    }

    public function afterProcessLogin($model)
    {

    }

    public function beforeProcessLogout($model)
    {

    }

    public function afterProcessLogout($model)
    {

    }
}
