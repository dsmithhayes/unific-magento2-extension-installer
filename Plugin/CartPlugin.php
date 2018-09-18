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
     * @param $email
     * @param $valid
     * @return array
     */
    public function beforeIsEmailAvailable($email, $valid)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::isEmailAvailable', 'before') as $request)
        {
            $emailObject = $this->dataObjectFactory->create();
            $emailObject->setEmail($email);

            $this->handleCondition($request->getId(), $request, $emailObject);
        }

        return [$email, $valid];
    }

    /**
     * @param $email
     * @param $valid
     * @return mixed
     */
    public function afterIsEmailAvailable($email, $valid)
    {
        foreach ($this->getRequestCollection('Magento\Customer\Api\AccountManagementInterface::isEmailAvailable') as $request)
        {
            $emailObject = $this->dataObjectFactory->create();
            $emailObject->setEmail($email);

            $this->handleCondition($request->getId(), $request, $emailObject);
        }

        return $valid;
    }
}
