<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Ui\DataProvider;

use Magento\Framework\Data\Collection;
use Magento\Framework\Api\Filter;

class RegularFilter extends \Magento\Framework\View\Element\UiComponent\DataProvider\RegularFilter
{
    /**
     * @param Collection $collection
     * @param Filter     $filter
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply(Collection $collection, Filter $filter)
    {
        if ($filter->getField() == 'status') {
            $filter->setField('status_preorder');
        }
        $collection->addFieldToFilter($filter->getField(), [$filter->getConditionType() => $filter->getValue()]);
    }
}
