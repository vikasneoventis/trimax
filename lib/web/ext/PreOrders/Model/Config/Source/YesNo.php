<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Aitoc\PreOrders\Model\Config\Source;

class YesNo implements \Magento\Framework\Option\ArrayInterface
{
    const YES = 2;
    const NO = 1;
    const USE_CONFIG = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::YES, 'label' => __('Yes')],
            ['value' => self::NO, 'label' => __('No')],
            ['value' => self::USE_CONFIG, 'label' => __('Use Config')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [self::USE_CONFIG => __('Use Config'), self::NO => __('No'), self::YES => __('Yes')];
    }
}
