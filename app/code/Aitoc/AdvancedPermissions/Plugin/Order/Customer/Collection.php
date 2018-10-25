<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Order\Customer;

class Collection
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Sales
     */
    protected $helper;

    /**
     * Collection constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Sales $helper
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Sales $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Customer\Collection $subject
     */
    public function beforeLoad(
        \Magento\Sales\Model\ResourceModel\Order\Customer\Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getShowAllCustomers()) {
            $allowedStoreIds = $this->helper->getAllowedStoreIds();
            $subject->addFieldToFilter('store_id', ['in' => $allowedStoreIds]);
        }
    }
    
    /**
     * Get SQL for get record count
     *
     * @return \Magento\Framework\DB\Select
     */
    public function afterGetSelectCountSql(\Magento\Sales\Model\ResourceModel\Order\Customer\Collection $subject, $result)
    {
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getShowAllCustomers()) {
            $result->where('e.store_id IN (?)', $this->helper->getAllowedStoreIds());
        }
        return $result;
    }
}
