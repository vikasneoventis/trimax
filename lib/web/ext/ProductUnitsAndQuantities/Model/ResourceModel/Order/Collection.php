<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'item_id';
    protected $_eventPrefix = 'aitoc_productunitsandquantities_order_collection';
    protected $_eventObject = 'order_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\ProductUnitsAndQuantities\Model\Order',
            'Aitoc\ProductUnitsAndQuantities\Model\ResourceModel\Order'
        );
    }
}
