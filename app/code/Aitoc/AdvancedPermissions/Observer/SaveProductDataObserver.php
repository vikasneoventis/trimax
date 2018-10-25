<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * @event eav_collection_abstract_load_before
 */
class SaveProductDataObserver implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    protected $attrGlobal;

    /**
     * EavCollection constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(\Aitoc\AdvancedPermissions\Helper\Data $helper)
    {
        $this->helper     = $helper;
        $this->attrGlobal = ['sku', 'price', 'qty', 'is_in_stock'];
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($this->helper->isAdvancedPermissionEnabled()) {
            if (!$this->helper->getRole()->getManageGlobalAttribute()) {
                foreach ($this->attrGlobal as $value) {
                    $product->unsetData($value);
                }
            }
        }

        return;
    }
}
