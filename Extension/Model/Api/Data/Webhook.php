<?php

namespace Unific\Extension\Model\Api\Data;

class Webhook implements \Unific\Extension\Api\Data\IntegrationInterface
{
    private $actionmapping = array(
        'customer_login' => 'Magento\Customer\Model\Session::setCustomerAsLoggedIn',
        'customer_logout' => 'Magento\Customer\Model\Session::logout',
        'admin_login' => 'Magento\Backend\Model\Auth\Session::processLogin',
        'admin_logout' => 'Magento\Backend\Model\Auth\Session::processLogout',
        'customer_create' => 'Magento\Customer\Api\CustomerManagementInterface::save',
        'customer_update' => 'Magento\Customer\Api\CustomerManagementInterface::save',
        'admin_user_create' => 'Magento\User\Model\User::save',
        'quote_create' => 'Magento\Quote\Api\CartManagementInterface::save',
        'quote_update' => 'Magento\Quote\Api\CartManagementInterface::save',
        'order_create' => 'Magento\Sales\Api\OrderManagementInterface::place',
        'invoice_create' => 'Magento\Sales\Model\Order\Invoice::capture',
        'creditmemo_create' => 'Magento\Sales\Model\Order\Creditmemo::save',
        'shipment_create' => 'Magento\Shipment\Model\Shipment::save',
        'category_create' => 'Magento\Catalog\Api\CategoryManagementInterface::save',
        'category_update' => 'Magento\Catalog\Api\CategoryManagementInterface::save',
        'product_create' => 'Magento\Catalog\Api\ProductManagementInterface::save',
        'product_update' => 'Magento\Catalog\Api\ProductManagementInterface::save'
    );

    protected $name;
    protected $description;
    protected $event;
    protected $eventExecution;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return mixed
     */
    public function getEventExecution()
    {
        return $this->eventExecution;
    }

    /**
     * @param mixed $eventExecution
     */
    public function setEventExecution($eventExecution)
    {
        $this->eventExecution = $eventExecution;
    }
}