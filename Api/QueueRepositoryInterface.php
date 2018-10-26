<?php

namespace Unific\Extension\Api;

interface QueueRepositoryInterface
{
    /**
     * Create or update a data
     * @param Data\QueueInterface $queue
     */
    public function save(Data\QueueInterface $queue);

    /**
     * @param $queueGuid
     * @return Data\QueueInterface $queue
     */
    public function getById($queueGuid);

    /**
     * Delete test.
     * @param Data\QueueInterface $queue
     */
    public function delete(Data\QueueInterface $queue);

    /**
     * Delete test by ID.
     * @return bool
     */
    public function deleteById($queueGuid);

    /**
     * Truncate the historical queue
     * @return boolean
     */
    public function truncateHistorical();

    /**
     * Truncate the queue
     * @return boolean
     */
    public function truncateQueue();
}