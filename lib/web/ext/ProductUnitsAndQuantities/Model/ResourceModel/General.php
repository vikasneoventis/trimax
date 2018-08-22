<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class General extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aitoc_product_units_and_quantities', 'item_id');
    }
}
