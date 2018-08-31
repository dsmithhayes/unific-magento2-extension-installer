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
     * @return mixed
     */
    public function setProtocol($protocol = "rest");

    /**
     * @return mixed
     */
    public function getUrl();

    /**
     * @param $url
     * @return mixed
     */
    public function setUrl($url);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param string $type
     * @return mixed
     */
    public function setType($type = "get");
}