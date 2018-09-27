<?php

namespace Unific\Extension\Api\Data\Webhook;

use Magento\Framework\Api\ExtensibleDataInterface;

interface RequestInterface extends ExtensibleDataInterface
{
    /**
     * @return mixed
     */
    public function getProtocol();

    /**
     * @param string $protocol
     * @return void
     */
    public function setProtocol($protocol = "rest");

    /**
     * @return mixed
     */
    public function getUrl();

    /**
     * @param $url
     * @return void
     */
    public function setUrl($url);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param string $type
     * @return void
     */
    public function setType($type = "get");

    /**
     * @return mixed
     */
    public function getWebhook();

    /**
     * @param string $type
     * @return void
     */
    public function setWebhook($webhook = "order");
}