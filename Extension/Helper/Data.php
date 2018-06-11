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
     * Login the user and create it if it doesnt exist yet
     *
     * @param $connection
     * @throws Zend_Exception
     */
    public function loginUser($connection)
    {
        $userData = $this->getMappings($connection->getUserData());

        $oCustomer = $this->objectManager->get('Magento\Customer\Model\Customer');
        $oCustomer->setStore($this->objectManager->get('\Magento\Store\Model\StoreManagerInterface')->getStore());
        $oCustomer->loadByEmail($userData['username']);

        if(!$oCustomer->getId()) {
            // Preparing data for new customer
            $oCustomer->setEmail($userData['username']);
            $oCustomer->setFirstname($userData['firstname']);
            $oCustomer->setLastname($userData['lastname']);
            $oCustomer->setPassword($this->randomPassword());

            // Save data
            $oCustomer->save();
            $oCustomer->loadByEmail($userData['username']);
        }

        // Login user
        $customerSession = $this->objectManager->get('Magento\Customer\Model\Session');
        $customerSession->setCustomerAsLoggedIn($oCustomer);

        header('Location: /customer/account/login');
    }

    protected function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Login the user and create it if it doesnt exist yet
     *
     * @param $connection
     * @throws Zend_Exception
     */
    public function loginAdminUser($connection)
    {
        $userData = $this->getMappings($connection->getUserData());

        $oUser = $this->objectManager->get('Magento\User\Model\User');
        $oUser->loadByUsername($userData['username']);

        if(!$oUser->getId()) {
            $oUser->setUsername($userData['username']);
            $oUser->setEmail($userData['username']);
            $oUser->setFirstname($userData['firstname']);
            $oUser->setLastname($userData['lastname']);
            $oUser->setPassword($this->randomPassword().'123');
            $oUser->setRoleId(1);
            $oUser->save();

            $oUser->loadByUsername($userData['username']);
        }

        // Login the admin user
        $session = $this->objectManager->get('Magento\Backend\Model\Auth\Session');
        $session->setUser($oUser);
        $session->processLogin();

        if ($session->isLoggedIn()) {
            $cookieManager = $this->objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
            $cookieValue = $session->getSessionId();
            if ($cookieValue) {
                $sessionConfig = $this->objectManager->get('Magento\Backend\Model\Session\AdminConfig');
                $cookiePath = str_replace('autologin.php', 'index.php', $sessionConfig->getCookiePath());
                $cookieMetadata = $this->objectManager->get('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory')
                    ->createPublicCookieMetadata()
                    ->setDuration(3600)
                    ->setPath($cookiePath)
                    ->setDomain($sessionConfig->getCookieDomain())
                    ->setSecure($sessionConfig->getCookieSecure())
                    ->setHttpOnly($sessionConfig->getCookieHttpOnly());
                $cookieManager->setPublicCookie($session->getName(), $cookieValue, $cookieMetadata);

                if(class_exists('Magento\Security\Model\AdminSessionsManager')) {
                    $adminSessionManager = $this->objectManager->get('Magento\Security\Model\AdminSessionsManager');
                    $adminSessionManager->processLogin();
                }
            }

            header('Location: /admin/');
        }
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
