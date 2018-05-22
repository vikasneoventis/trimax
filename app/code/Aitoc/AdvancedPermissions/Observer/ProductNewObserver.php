<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ProductNewObserver implements ObserverInterface
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
     * Observer constructor.
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
            $ids = $this->helper->getAllowedWebsiteIds();
            if (count($ids)) {
                $product->setWebsiteIds([$ids[0]]);
            }
        }
    }
}
