<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class QtyType extends AbstractSource
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Static')],
            ['value' => 1, 'label' => __('Dynamic')],
        ];
    }

    public function toArray()
    {
        return [
            0 => __('Static'),
            1 => __('Dynamic')
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
