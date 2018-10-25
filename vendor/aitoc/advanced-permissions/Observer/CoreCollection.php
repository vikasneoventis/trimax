<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * @event core_collection_abstract_load_before
 */
class CoreCollection implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Sales
     */
    protected $salesHelper;
    
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;
    
    /**
     * CoreCollection constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Sales $salesHelper
     * @param \Aitoc\AdvancedPermissions\Helper\Data  $helper
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Sales $salesHelper,
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        $this->salesHelper = $salesHelper;
        $this->helper      = $helper;
    }

    /**
     * Add additional filters to a collection for restrict store view.
     *
     * @event core_collection_abstract_load_before
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getEvent()->getCollection();
        if ($collection->getResource() instanceof \Magento\Customer\Model\ResourceModel\Customer) {
            $showAll = $this->helper->getRole()->getShowAllCustomers();
            if (!$showAll) {
                $collection->getSelect()
                    ->joinLeft(['ce_t' => 'customer_entity'], "ce_t.entity_id = main_table.entity_id", ["store_id" => "ce_t.store_id"]);
            }
        }
        if ($this->salesHelper->isSalesResource($collection->getResource())
            && $this->salesHelper->isAdvancedPermissionEnabled()
        ) {
            $this->salesHelper->addAllowedProductFilter($collection);
        }
    }
}
