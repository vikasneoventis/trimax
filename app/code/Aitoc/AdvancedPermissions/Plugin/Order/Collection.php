<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Order;

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
     * @param \Magento\Reports\Model\ResourceModel\Order\Collection $subject
     * @param \Closure                                              $work
     * @param                                                       $field
     * @param                                                       $condition
     */
    public function aroundAddFieldToFilter(
        \Magento\Reports\Model\ResourceModel\Order\Collection $subject,
        \Closure $work,
        $field,
        $condition
    ) {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            if ($field == 'created_at') {
                $field = 'main_table.' . $field;
            }
        }

        return $work($field, $condition);
    }
}
