<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

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

                $select = $collection->getSelect();

                if ($where = $select->getPart('where')) {
                    foreach ($where as $key=> $condition) {

                        $field =  $this->getStringBetween($condition,'`','`');
                        if ($field && strpos($condition, $field) && !strpos($condition,'main_table') && !strpos($condition,'store_id')) {
                            $new_condition = str_replace($field, "main_table.".$field, $condition);
                            $where[$key] = $new_condition;
                        }
                    }
                    $select->setPart('where', $where);
                }
            }
        }
        if ($this->salesHelper->isSalesResource($collection->getResource())
            && $this->salesHelper->isAdvancedPermissionEnabled()
        ) {
            $this->salesHelper->addAllowedProductFilter($collection);
        }
    }

    public function getStringBetween($str,$from,$to)
    {
        $sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));

        return $from . substr($sub,0, strrpos($sub,$to)) . $to;

    }
}
