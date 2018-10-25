<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ProductBeforeSaveObserver implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productloader;

    /**
     * InventoryObserver constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param \Magento\Catalog\Model\ProductFactory  $productloader
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Catalog\Model\ProductFactory $productloader
    ) {
        $this->helper = $helper;
        $this->productloader = $productloader;
    }

    /**
     * @param EventObserver $observer
     *
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $product = $observer->getEvent()->getProduct();
            $entity  = $this->productloader->create()->load($product->getId());
            // fix: append non-manageable websites while sub-admin save product
            $disallowed = array_diff($entity->getWebsiteIds(), $this->helper->getAllowedWebsiteIds());
            $product->setWebsiteIds(array_unique(array_merge($disallowed, $product->getWebsiteIds())));
        }
        return $this;
    }
}
