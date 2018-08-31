<?php

namespace Unific\Extension\Model\Api\Data\Webhook;

class Condition implements \Unific\Extension\Api\Data\Webhook\ConditionInterface
{
    /**
     * @var
     */
    protected $condition;

    /**
     * @var
     */
    protected $comparison;

    /**
     * @var
     */
    protected $value;

    /**
     * @var string
     */
    protected $action = 'nothing';

    /**
     * @var
     */
    protected $request;

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getComparison()
    {
        return $this->comparison;
    }

    /**
     * @param string $comparison
     */
    public function setComparison($comparison)
    {
        $this->comparison = $comparison;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return \Unific\Extension\Api\Data\Webhook\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Unific\Extension\Api\Data\Webhook\RequestInterface $request
     */
    public function setRequest(\Unific\Extension\Api\Data\Webhook\RequestInterface $request)
    {
        $this->request = $request;
    }
}