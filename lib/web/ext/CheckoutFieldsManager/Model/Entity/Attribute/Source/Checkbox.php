<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Model\Entity\Attribute\Source;

class Checkbox extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $options = parent::getAllOptions();
        $newOptions = [];
        foreach ($options as $item) {
            if ($item['value']) {
                $newOptions[] = $item;
            }
        }

        return $newOptions;
    }
}
