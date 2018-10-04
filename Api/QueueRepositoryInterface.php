<?php

namespace Unific\Extension\Api;

interface QueueRepositoryInterface
{
    /**
     * Create or update a data
     */
    public function save(\Unific\Extension\Api\Data\QueueInterface $queue);

    public function getById($queueGuid);

    /**
     * Delete test.
     */
    public function delete(\Unific\Extension\Api\Data\QueueInterface $queue);

    /**
     * Delete test by ID.
     */
    public function deleteById($queueGuid);
}