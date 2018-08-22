<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model;

class SourceBackorders extends \Magento\CatalogInventory\Model\Source\Backorders
{
    const BACKORDERS_YES_PREORDERS = 30;

    const BACKORDERS_YES_PREORDERS_ZERO = 35;

    /**
     * Get array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        $options[] = [
            'value' => self::BACKORDERS_YES_PREORDERS,
            'label'=>__('Pre-Orders')
        ];
        $options[] = [
            'value' => self::BACKORDERS_YES_PREORDERS_ZERO,
            'label'=>__('Pre-Order for Out-Of-Stock')
        ];

        return $options;
    }
}
