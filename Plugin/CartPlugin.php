<?php

namespace Unific\Extension\Plugin;

class CartPlugin extends AbstractPlugin
{
    protected $entity = 'cart';
    protected $subject = 'cart/create';

    protected $dataObjectFactory;

    /**
     * @param \Unific\Extension\Logger\Logger $logger
     * @param \Unific\Extension\Helper\Mapping $mapping
     * @param \Unific\Extension\Connection\Rest\Connection $restConnection
     * @param \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory
     * @param \Unific\Extension\Model\RequestFactory $requestFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        \Unific\Extension\Logger\Logger $logger,
        \Unific\Extension\Helper\Mapping $mapping,
        \Unific\Extension\Connection\Rest\Connection $restConnection,
        \Unific\Extension\Model\ResourceModel\Request\Grid\CollectionFactory $collectionFactory,
        \Unific\Extension\Model\RequestFactory $requestFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    )
    {
        $this->dataObjectFactory = $dataObjectFactory;

        parent::__construct($logger, $mapping, $restConnection, $collectionFactory, $requestFactory);
    }


    /**
     * @param $subject
     * @param callable $proceed
     * @param $email
     * @param null $websiteId
     * @return array
     */
    public function aroundIsEmailAvailable($subject, callable $proceed, $email, $websiteId = null)
    {
        foreach ($this->getRequestCollection($this->subject, 'before') as $request)
        {
            $emailObject = $this->dataObjectFactory->create();
            $emailObject->setEmail($email);

            $this->handleCondition($request->getId(), $request, $emailObject);
        }

        $returnValue = $proceed($email, $websiteId);

        foreach ($this->getRequestCollection($this->subject) as $request)
        {
            $emailObject = $this->dataObjectFactory->create();
            $emailObject->setEmail($email);

            $this->handleCondition($request->getId(), $request, $emailObject);
        }

        return $returnValue;
    }
}
