<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class Mapping
 * @package Unific\Extension\Helper
 */
class Mapping extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleDirReader;

    /**
     * @var \Magento\Framework\Xml\Parser
     */
    private $parser;

    /**
     * Mapping constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Module\Dir\Reader $moduleDirReader
     * @param \Magento\Framework\Xml\Parser $parser
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Magento\Framework\Xml\Parser $parser
    )
    {
        parent::__construct($context);

        $this->moduleDirReader = $moduleDirReader;
        $this->parser = $parser;
    }

    public function getMappings()
    {
        $filePath = $this->moduleDirReader->getModuleDir('etc', 'Unific_Extension') . '/mappings.xml';
        $parsedArray = $this->parser->load($filePath)->xmlToArray();

        return $parsedArray;
    }
}
