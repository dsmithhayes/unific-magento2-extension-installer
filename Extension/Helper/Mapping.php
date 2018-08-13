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

    /**
     * @return mixed
     */
    public function getMappings()
    {
        $filePath = $this->moduleDirReader->getModuleDir('etc', 'Unific_Extension') . '/mappings.xml';
        $parsedArray = $this->parser->load($filePath)->xmlToArray();

        return $parsedArray;
    }

    /**
     * @param array $data
     * @param string $entity
     * @return array
     */
    public function map(array $data, $entity = 'order')
    {
        $mappedArray = array();

        foreach($this->getMappings()['mappings'][$entity] as $mapping => $settings)
        {
            if(isset($data[$mapping]))
            {
                if($settings == null || (is_array($settings) && (isset($settings['location']) == false || $settings['location'] == 'body')))
                {
                    $externalKey = ($settings == null || is_array($settings) && isset($settings['external']) == false) ? $mapping : $settings['external'];
                    $mappedArray[$externalKey] = $data[$mapping];
                }
            }
        }

        return $mappedArray;
    }
}
