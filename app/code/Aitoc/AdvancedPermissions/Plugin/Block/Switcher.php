<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Block;

class Switcher
{

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * Switcher constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param \Magento\Store\Model\WebsiteFactory    $websiteFactory
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    ) {
        $this->helper         = $helper;
        $this->websiteFactory = $websiteFactory;
    }


    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product $object
     * @param \Closure                                     $work
     * @param                                              $product
     *
     * @return mixed
     */
    public function aroundGetWebsiteCollection(\Magento\Backend\Block\Store\Switcher $object, \Closure $work)
    {
        $collection = $this->websiteFactory->create()->getResourceCollection();

        $websiteIds = $this->helper->getAllowedWebsiteIds();
        if ($websiteIds !== null) {
            $collection->addIdFilter($websiteIds);
        }

        return $collection->load();
    }

    /**
     * @param $object
     * @param $stores
     *
     * @return mixed
     */
    public function afterGetStores($object, $stores)
    {
        if ($storeIds = $this->helper->getAllowedStoreViewIds()) {
            foreach (array_keys($stores) as $storeId) {
                if (!in_array($storeId, $storeIds)) {
                    unset($stores[$storeId]);
                }
            }
        }

        return $stores;
    }

    /**
     * @param \Magento\Backend\Block\Store\Switcher $object
     * @param \Closure                              $work
     * @param                                       $group
     *
     * @return \Magento\Store\Model\ResourceModel\Store\Collection
     */
    public function aroundGetStoreCollection(\Magento\Backend\Block\Store\Switcher $object, \Closure $work, $group)
    {
        if (!$group instanceof \Magento\Store\Model\Group) {
            $group = $object->_storeGroupFactory->create()->load($group);
        }
        $stores    = $group->getStoreCollection();
        $_storeIds = $this->helper->getAllowedStoreViewIds();
        if (!empty($_storeIds)) {
            $stores->addIdFilter($_storeIds);
        }

        return $stores;
    }
}
