<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Order;

use Aitoc\AdvancedPermissions\Helper\Sales;
use Closure;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Reports\Model\ResourceModel\Order\Collection as OrderCollection;

class Collection
{
    /**
     * @var Sales
     */
    protected $helper;

    /**
     * Collection constructor.
     *
     * @param Sales $helper
     */
    public function __construct(
        Sales $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param OrderCollection $subject
     * @param Closure $work
     * @param string|array $field
     * @param null|string|array $condition
     * @return OrderCollection
     */
    public function aroundAddFieldToFilter(
        OrderCollection $subject,
        Closure $work,
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

    /**
     * @param OrderCollection $subject
     * @param string|Attribute $attribute
     * @param array|int|string|null $condition
     * @return array
     */
    public function beforeAddAttributeToFilter(OrderCollection $subject, $attribute, $condition = null)
    {
        if ($attribute == 'store_id') {
            $attribute = 'main_table.store_id';
        }

        return [$attribute, $condition];
    }
}
