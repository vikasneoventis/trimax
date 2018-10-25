<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\Store;

use Magento\Framework\App\ObjectManager;

class StoreManager extends \Magento\Store\Model\StoreManager
{

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * StoreManager constructor.
     *
     * @param \Magento\Store\Api\StoreRepositoryInterface        $storeRepository
     * @param \Magento\Store\Api\GroupRepositoryInterface        $groupRepository
     * @param \Magento\Store\Api\WebsiteRepositoryInterface      $websiteRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Api\StoreResolverInterface          $storeResolver
     * @param \Magento\Framework\Cache\FrontendInterface         $cache
     * @param \Aitoc\AdvancedPermissions\Helper\Data             $helper
     */
    public function __construct(
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Store\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Api\StoreResolverInterface $storeResolver,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        parent::__construct(
            $storeRepository,
            $groupRepository,
            $websiteRepository,
            $scopeConfig,
            $storeResolver,
            $cache,
            true
        );
        $this->helper = $helper;
    }


    /**
     * Get all stores
     *
     * @param bool $withDefault
     * @param bool $codeKey
     *
     * @return array
     */
    public function getStoresAll($withDefault = false, $codeKey = false)
    {
        $stores = [];
        $this->storeRepository->clean();
        foreach ($this->storeRepository->getList() as $store) {
            if (!$withDefault && $store->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $stores[$store->getCode()] = $store;
            } else {
                $stores[$store->getId()] = $store;
            }
        }

        return $stores;
    }

    /**
     * Get all websites
     *
     * @param bool $withDefault
     * @param bool $codeKey
     *
     * @return array
     */
    public function getWebsitesAll($withDefault = false, $codeKey = false)
    {
        $websites = [];
        foreach ($this->websiteRepository->getList() as $website) {
            if (!$withDefault && $website->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $websites[$website->getCode()] = $website;
            } else {
                $websites[$website->getId()] = $website;
            }
        }

        return $websites;
    }
}
