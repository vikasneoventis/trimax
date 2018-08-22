<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model;

class Order extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Prefix for events
     * @var string
     */
    protected $_eventPrefix = 'aitoc_productunitsandquantities_order';

    protected function _construct()
    {
        $this->_init('Aitoc\ProductUnitsAndQuantities\Model\ResourceModel\Order');
    }
}
