<?php

namespace Unific\Extension\Controller\Connect;
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var array
     */
    protected $config = array(
        'signing_method' => "HMAC_SHA_256",
        'client_id'  => '',
        'client_secret' => '',
        'access_token' => '',
        'tokenName' => 'oauth_token',
        'authenticationScheme' => 'query',
        'clientAuthenticationScheme' => 'form'
    );

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Unific\Extension\Helper\Data
     */
    protected $unificHelper;

    /**
     * Index constructor.
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Unific\Extension\Helper\Data $unificHelper
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Unific\Extension\Helper\Data $unificHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->unificHelper = $unificHelper;

        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $config = $this->config;
        $config['id'] = md5(uniqid());
        $config['client_id'] = $this->unificHelper->getApiUser()->getClientId();
        $config['client_secret'] = $this->unificHelper->getApiUser()->getClientSecret();
        $config['access_token'] = $this->unificHelper->getApiUser()->getAccessToken();
        
        return  $this->resultJsonFactory->create()->setData($config);
    }
}