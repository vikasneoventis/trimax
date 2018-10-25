<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

/**
 * @event eav_collection_abstract_load_before
 */
class EavCollection implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * EavCollection constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(\Aitoc\AdvancedPermissions\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Add additional filters to a collection for restrict store view.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        /**
         * Filtering Catalog Product Collection by allowed website ids
         */
        if ($collection instanceof ProductCollection) {
            if ($this->helper->isAdvancedPermissionEnabled()) {
                $allowedWebsites = $this->helper->getAllowedWebsiteIds();
                if (count($allowedWebsites)) {
                    $collection->addWebsiteFilter($allowedWebsites);
                }
            }
        }
        return;
    }
}
