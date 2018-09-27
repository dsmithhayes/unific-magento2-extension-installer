<?php

namespace Unific\Extension\Model\Api\Data\Webhook;

class Request implements \Unific\Extension\Api\Data\Webhook\RequestInterface
{
    protected $protocol = 'rest';
    protected $url = '';
    protected $type = 'get';
    protected $webhook = 'order';

    /**
     * @return string
     */
    public function getWebhook()
    {
        return $this->webhook;
    }

    /**
     * @param string $webhook
     */
    public function setWebhook($webhook = 'order')
    {
        $this->webhook = $webhook;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     */
    public function setProtocol($protocol = "rest")
    {
        $this->protocol = $protocol;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type = "get")
    {
        $this->type = $type;
    }
}