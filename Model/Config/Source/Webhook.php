<?php
/**
 * Used in creating options for Server Type config value selection
 *
 */
namespace Wizkunde\WebSSO\Model\Config\Source;

class Webhook implements \Magento\Framework\Option\ArrayInterface
{
    protected $webhooks =  array(
        'customer/login' => 'Magento\Customer\Model\Session::setCustomerDataAsLoggedIn',
        'customer/logout' => 'Magento\Customer\Model\Session::logout',
        'admin/login' => 'Magento\Backend\Model\Auth\Session::processLogin',
        'admin/logout' => 'Magento\Backend\Model\Auth\Session::processLogout',
        'customer/create' => 'Magento\Customer\Api\CustomerRepositoryInterface::save',
        'customer/update' => 'Magento\Customer\Api\CustomerRepositoryInterface::save',
        'admin/user/create' => 'Magento\User\Model\User::save',
        'cart/create' => 'Magento\Customer\Api\AccountManagementInterface::isEmailAvailable',
        'order/create' => 'Magento\Sales\Api\OrderManagementInterface::place',
        'order/cancel' => 'Magento\Sales\Api\OrderManagementInterface::cancel',
        'order/update' => 'Magento\Sales\Api\OrderManagementInterface::place',
        'order/invoice' => 'Magento\Sales\Model\Order\Invoice::capture',
        'order/refund' => 'Magento\Sales\Api\CreditmemoManagementInterface::save',
        'order/ship' => 'Magento\Sales\Api\ShipmentManagementInterface::save',
        'category/create' => 'Magento\Catalog\Model\Category::save',
        'category/update' => 'Magento\Catalog\Model\Category::save',
        'product/create' => 'Magento\Catalog\Model\Product::save',
        'product/update' => 'Magento\Catalog\Model\Product::save'
    );

    protected $options =  array(
        'customer/login' => 'Customer Login',
        'customer/logout' => 'Customer Logout',
        'admin/login' => 'Admin User Login',
        'admin/logout' => 'Admin User Logout',
        'customer/create' => 'Create a Customer',
        'customer/update' => 'Update a Customer',
        'admin/user/create' => 'Create Admin User',
        'cart/create' => 'Abandoned Checkout Create',
        'order/create' => 'Create an Order',
        'order/cancel' => 'Cancel an Order',
        'order/update' => 'Update an Order',
        'invoice/create' => 'Invoice an Order',
        'order/refund' => 'Refund an Order',
        'order/ship' => 'Ship an Order',
        'category/create' => 'Create a Category',
        'category/update' => 'Update a Category',
        'product/create' => 'Create a Product',
        'product/update' => 'Update a Product'
    );

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        foreach($this->options as $key => $value)
        {
            $optionArray[] = array('value' => $value, 'label' => $key);
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }

    public function getWebhooks()
    {
        return $this->webhooks;
    }

    public function getOptions()
    {
        return $this->options;
    }
}
