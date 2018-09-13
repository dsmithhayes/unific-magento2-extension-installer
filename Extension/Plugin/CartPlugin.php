<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'cart/create';

    protected $dataObjectFactory;

    /**
     * OrderPlugin constructor.
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    )
    {
        $this->logger = $logger;
        $this->mappingHelper = $mapping;
        $this->restConnection = $restConnection;
        $this->dataObjectFactory = $dataObjectFactory;

        parent::__construct($logger, $mapping, $restConnection);
    }

    /**
     * @param $subject
     * @param $email
     * @param null $websiteId
     * @return array
     */
    public function beforeIsEmailAvailable($subject, $email, $websiteId = null)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::isEmailAvailable', 'before') as $request)
        {
            $emailObject = $this->dataObjectFactory->create();
            $emailObject->setEmail($email);
            $emailObject->setWebsiteId($websiteId);

            $this->handleCondition($request->getId(), $request, $emailObject);
        }

        return [$email, $websiteId];
    }

    /**
     * @param $subject
     * @param $email
     * @param null $websiteId
     * @return mixed
     */
    public function afterIsEmailAvailable($subject, $email, $websiteId = null)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::isEmailAvailable') as $request)
        {
            $emailObject = $this->dataObjectFactory->create();
            $emailObject->setEmail($email);
            $emailObject->setWebsiteId($websiteId);

            $this->handleCondition($request->getId(), $request, $emailObject);
        }

        return $email;
    }
}
