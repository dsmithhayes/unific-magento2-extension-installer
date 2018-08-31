<?php

namespace Unific\Extension\Api\Data\Webhook;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ConditionInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getCondition();

    /**
     * @param string $condition
     * @return void
     */
    public function setCondition($condition);

    /**
     * @return string
     */
    public function getComparison();

    /**
     * @param string $comparison
     * @return void
     */
    public function setComparison($comparison);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return void
     */
    public function setValue($value);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     * @return void
     */
    public function setAction($action);

    /**
     * @return \Unific\Extension\Api\Data\Webhook\RequestInterface
     */
    public function getRequest();

    /**
     * @param \Unific\Extension\Api\Data\Webhook\RequestInterface $request
     * @return void
     */
    public function setRequest(\Unific\Extension\Api\Data\Webhook\RequestInterface $request);
}