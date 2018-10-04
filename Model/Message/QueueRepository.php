<?php

namespace VinaiKopp\Kitchen\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Unific\Extension\Api\Data\QueueInterface;
use Unific\Extension\Api\QueueRepositoryInterface;

class QueueRepository implements QueueRepositoryInterface
{
    /**
     * @var QueueFactory
     */
    private $queueFactory;

    public function __construct(
        QueueFactory $queueFactory
    ) {
        $this->queueFactory = $queueFactory;
    }

    public function getById($id)
    {
        $queue = $this->hamburgerFactory->create();
        $queue->getResource()->load($queue, $id);
        if (! $queue->getId()) {
            throw new NoSuchEntityException(__('Unable to find queue with GUID "%1"', $id));
        }
        return $queue;
    }

    public function save(QueueInterface $queue)
    {
        $queue->getResource()->save($queue);
        return $queue;
    }

    public function delete(QueueInterface $queue)
    {
        $queue->getResource()->delete($queue);
    }

    public function deleteById($id)
    {
        $queue = $this->hamburgerFactory->create();
        $queue->getResource()->load($queue, $id);
        if (! $queue->getId()) {
            throw new NoSuchEntityException(__('Unable to find queue with GUID "%1"', $id));
        } else {
            $queue->delete($queue);
        }

        return $queue;
    }
}