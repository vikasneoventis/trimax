<?php
namespace Evincemage\Customerredirection\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_CUSTOMER_REDIRECT = 'customer/startup/redirection';

    /**
     * @var StoreManager
     */
    public $storeManager;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManager $storeManager
     */
    public function __construct(Context $context, StoreManager $storeManager)
    {
        
        $this->storeManager     = $storeManager;
        parent::__construct($context);
    }
    
    public function CustomerRedirect()
    {
        return $this->scopeConfig->getValue(self::XML_CUSTOMER_REDIRECT, ScopeInterface::SCOPE_STORE);
    }

    public function RedirectSetting()
    {
       
       return $this->getScopeConfig()->getValue('customer/startup/redirect_dashboard',ScopeInterface::SCOPE_STORE);
    }

     private function getScopeConfig()
    {
        if (!($this->scopeConfig instanceof \Magento\Framework\App\Config\ScopeConfigInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\App\Config\ScopeConfigInterface::class
            );
        } else {
            return $this->scopeConfig;
        }
    }
   
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}