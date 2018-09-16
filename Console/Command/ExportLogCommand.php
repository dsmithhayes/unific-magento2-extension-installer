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
 * Class ExportLogCommand
 */
class ExportLogCommand extends Command
{
    protected $objectManager;
    protected $logger;
    protected $auditLogCollectionFactory;

    protected $exportDays = 7;

    /**
     * CleanLogCommand constructor.
     * @param ObjectManagerInterface $manager
     * @param \Unific\Extension\Model\ResourceModel\Audit\Log\CollectionFactory $auditLogCollectionFactory ,
     * @param \Unific\Extension\Logger\Logger $logger
     */
    public function __construct(
        ObjectManagerInterface $manager,
        \Unific\Extension\Model\ResourceModel\Audit\Log\CollectionFactory $auditLogCollectionFactory,
        \Unific\Extension\Logger\Logger $logger
    )
    {
        $this->objectManager = $manager;
        $this->auditLogCollectionFactory = $auditLogCollectionFactory;
        $this->logger = $logger;

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
        $this->setName('unific:log:export')
            ->setDescription('Export a part of the MySQL Audit Log')
            ->setDefinition(
                new InputDefinition(array(
                    new InputArgument('days', InputOption::VALUE_OPTIONAL, 'How many days will be exported')
                ))
            );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('days')) {
            $this->exportDays = $input->getArgument('days');
        }

        $dateTime = new \DateTime();
        $dateTime->sub(new \DateInterval('P' . $this->preserveDays . 'D'));

        $collection = $this->auditLogCollectionFactory->create()
            ->addFieldToFilter('created_at', ['gteq' => $dateTime->format('Y-m-d H:i:s')]);

        // Lets write all of the items to a string
        $output->writeln("Exporting a total of " . $collection->count() . " items");

        foreach ($collection as $message) {
            $this->logger->info(
                implode(',', $message->getData())
            );
        }


    }
}