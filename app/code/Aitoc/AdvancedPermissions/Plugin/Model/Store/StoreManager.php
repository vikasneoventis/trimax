<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Model\Store;

class StoreManager
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Sales
     */
    protected $helper;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $auth;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\Store\StoreManager
     */
    protected $storeManager;

    /**
     * StoreManager constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Sales             $helper
     * @param \Magento\Backend\Model\Auth\Session                 $auth
     * @param \Aitoc\AdvancedPermissions\Model\Store\StoreManager $storeManager
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Sales $helper,
        \Magento\Backend\Model\Auth\Session $auth,
        \Aitoc\AdvancedPermissions\Model\Store\StoreManager $storeManager
    ) {
        $this->helper = $helper;
        $this->auth = $auth;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Store\Model\StoreManager $subject
     * @param                                   $result
     */
    public function aroundGetStores(
        \Magento\Store\Model\StoreManager $subject,
        \Closure $work,
        $withDefault = false,
        $codeKey = false
    ) {
        if ($this->auth->getUser() &&
            $this->helper->isAdvancedPermissionEnabled() &&
            $this->helper->isAdvancedPermissionAllowed()
        ) {
            $oldStores = $work($withDefault, $codeKey);
            $AllowedStoreViewIds = $this->helper->getAllowedStoreViewIds();
            $AllowedStoreViewCodes = $this->helper->getAllowedStoreViewIds(true);
            $stores = [];
            foreach ($oldStores as $key => $value) {
                if ($codeKey && in_array($key, $AllowedStoreViewCodes)) {
                    $stores[$key] = $value;
                }
                if (!$codeKey && in_array($key, $AllowedStoreViewIds)) {
                    $stores[$key] = $value;
                }
            }

            return $stores;
        }

        return $this->storeManager->getStoresAll($withDefault, $codeKey);
    }

    /**
     * @param \Magento\Store\Model\StoreManager $subject
     * @param                                   $result
     *
     * @return bool
     */
    public function afterHasSingleStore(\Magento\Store\Model\StoreManager $subject, $result)
    {
        if ($result) {
            return count($subject->getStores(true)) < 2;
        }
        
        return $result;
    }

    /**
     * @param \Magento\Store\Model\StoreManager $subject
     * @param \Closure                          $work
     * @param bool                              $withDefault
     * @param bool                              $codeKey
     *
     * @return array
     */
    public function aroundGetWebsites(
        \Magento\Store\Model\StoreManager $subject,
        \Closure $work,
        $withDefault = false,
        $codeKey = false
    ) {
        $websitesOld = $work($withDefault, $codeKey);
        if ($this->auth->getUser() && $this->helper->isAdvancedPermissionEnabled()) {
            $websitesIds = $this->helper->getAllowedWebsiteIds();
            $websitesCode = $this->helper->getAllowedWebsiteIds(true);
            $websites = [];
            foreach ($websitesOld as $key => $value) {
                if ($codeKey && in_array($key, $websitesCode)) {
                    $websites[$key] = $value;
                }
                if (!$codeKey && in_array($key, $websitesIds)) {
                    $websites[$key] = $value;
                }
            }

            return $websites;
        }

        return $websitesOld;
    }
}
