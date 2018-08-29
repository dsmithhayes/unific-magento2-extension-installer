<?php

namespace Unific\Extension\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $objectManager;
    protected $integrationFactory;
    protected $oauthService;
    protected $authorizationService;
    protected $oauthTokenModel;

    //Set your Data
    protected $apiIntegrationName = 'Unific-Integration';
    protected $apiIntegrationEmail = 'info@unific.com';
    protected $apiEndpoint = 'https://api.unific.com/';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Integration\Model\IntegrationFactory $integrationFactory,
        \Magento\Integration\Model\OauthService $oauthService,
        \Magento\Integration\Model\AuthorizationService $authorizationService,
        \Magento\Integration\Model\Oauth\Token $oauthTokenModel
    )
    {
        parent::__construct($context);

        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->integrationFactory = $integrationFactory;
        $this->oauthService = $oauthService;
        $this->authorizationService = $authorizationService;
        $this->oauthTokenModel = $oauthTokenModel;
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

        foreach ($serverObject->getData('mappings') as $mapping) {
            if (strpos($mapping['external'], '.') !== false) {
                $fields = explode('.', $mapping['external']);
                $dataSet = $userData;
                foreach ($fields as $i => $field) {
                    if ($i == count($fields) - 1) {
                        if (isset($dataSet[$field])) {
                            $returnData[$mapping['internal']] = $dataSet[$field];
                        }
                    } else {
                        if (isset($dataSet[$field])) {
                            $dataSet = (array)$dataSet[$field];
                        }
                    }
                }
            } else {
                if (isset($userData[$mapping['external']])) {
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

    public function getApiUser()
    {
        return $this->integrationFactory->create()->load($this->apiIntegrationName,'name')->getData();
    }

    /**
     * Create a new API User for Unific to work with
     */
    public function createApiUser()
    {
        $integrationExists = $this->integrationFactory->create()->load($this->apiIntegrationName,'name')->getData();

        if(empty($integrationExists)){
            $integrationData = array(
                'name' => $this->apiIntegrationName,
                'email' => $this->apiIntegrationEmail,
                'status' => '1',
                'endpoint' => $this->apiEndpoint,
                'setup_type' => '0'
            );
            try{
                // Code to create Integration
                $integrationFactory = $this->integrationFactory->create();
                $integration = $integrationFactory->setData($integrationData);
                $integration->save();
                $integrationId = $integration->getId();$consumerName = 'Integration' . $integrationId;


                // Code to create consumer
                $consumer = $this->oauthService->createConsumer(['name' => $consumerName]);
                $consumerId = $consumer->getId();
                $integration->setConsumerId($consumer->getId());
                $integration->save();


                // Code to grant permission
                $this->authorizationService->grantAllPermissions($integrationId);


                // Code to Activate and Authorize
                $uri = $this->oauthTokenModel->createVerifierToken($consumerId);
                $this->oauthTokenModel->setType('access');
                $this->oauthTokenModel->save();

            }catch(Exception $e){
                echo 'Error : '.$e->getMessage();
            }
        }
    }
}
