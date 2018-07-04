<?php

namespace Unific\Extension\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \Magento\Framework\ObjectManagerInterface;

/**
 * Class CleanLogCommand
 */
class CleanLogCommand extends Command
{
    protected $objectManager;

    protected $preserveDays = 7;
    protected $auditLogCollectionFactory;

    /**
     * CleanLogCommand constructor.
     * @param ObjectManagerInterface $manager
     * @param Unific\Extension\Model\ResourceModel\Audit\Log\CollectionFactory $auditLogCollectionFactory ,
     */
    public function __construct(
        ObjectManagerInterface $manager,
        \Unific\Extension\Model\ResourceModel\Audit\Log\CollectionFactory $auditLogCollectionFactory)
    {
        $this->objectManager = $manager;
        $this->auditLogCollectionFactory = $auditLogCollectionFactory;

        parent::__construct();
    }

    /**
     * @return ObjectManagerInterface
     */
    protected function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('unific:log:clean')
            ->setDescription('Clean the MySQL Audit Log')
            ->setDefinition(
                new InputDefinition(array(
                    new InputArgument('preserve', InputOption::VALUE_OPTIONAL, 'How many days will be preserved')
                ))
            );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('preserve')) {
            $this->preserveDays = $input->getArgument('preserve');
        }

        $dateTime = new \DateTime();
        $dateTime->sub(new \DateInterval('P' . $this->preserveDays . 'D'));

        $collection = $this->auditLogCollectionFactory->create()
            ->addFieldToFilter('created_at', ['lteq' => $dateTime->format('Y-m-d H:i:s')]);

        // Delete everything from this collection
        $collection->delete();
    }
}