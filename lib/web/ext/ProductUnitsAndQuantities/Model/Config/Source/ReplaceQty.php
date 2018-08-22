<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class ReplaceQty extends AbstractSource
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Off')],
            ['value' => 1, 'label' => __('Dropdown')],
            ['value' => 2, 'label' => __('Slider')],
            ['value' => 3, 'label' => __('Plus Minus')],
            ['value' => 4, 'label' => __('Arrows')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Off'), 1 => __('Dropdown'), 2 => __('Slider'), 3 => __('Plus Minus'), 4 => __('Arrows')];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
