<?php

namespace Unific\Extension\Plugin;

class CartPlugin
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

    public function beforeSave($model)
    {

    }

    public function afterSave($model)
    {

    }
}
