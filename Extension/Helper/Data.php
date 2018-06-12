<?php
namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;

    public function __construct(\Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Return the mapped mapping data
     *
     * @param $userData
     * @return mixed
     */
    public function getMappings($requestId, $userData)
    {
        $returnData = $userData;

        $serverObject = $this->objectManager->create('Unific\Extension\Model\Request');
        $serverObject->load($requestId);

        foreach($serverObject->getData('mappings') as $mapping) {
            if(strpos($mapping['external'], '.') !== false) {
                $fields = explode('.', $mapping['external']);
                $dataSet = $userData;
                foreach($fields as $i => $field) {
                    if($i == count($fields)-1) {
                        if(isset($dataSet[$field])) {
                            $returnData[$mapping['internal']] = $dataSet[$field];
                        }
                    } else {
                        if(isset($dataSet[$field])) {
                            $dataSet = (array)$dataSet[$field];
                        }
                    }
                }
            } else {
                if(isset($userData[$mapping['external']])) {
                    $returnData[$mapping['internal']] = $userData[$mapping['external']];
                }
            }
        }

        return $returnData;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
