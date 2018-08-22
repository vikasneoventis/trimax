<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model\ResourceModel\General;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'item_id';
    protected $_eventPrefix = 'aitoc_productunitsandquantities_general_collection';
    protected $_eventObject = 'general_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\ProductUnitsAndQuantities\Model\General',
            'Aitoc\ProductUnitsAndQuantities\Model\ResourceModel\General'
        );
    }
}
