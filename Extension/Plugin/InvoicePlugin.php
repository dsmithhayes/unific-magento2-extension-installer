<?php

namespace Unific\Extension\Plugin;

class InvoicePlugin
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

    public function beforeCapture($model)
    {

    }

    public function afterCapture($model)
    {

    }
}
