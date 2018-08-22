<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\MultiLocationInventory\Model\Supplier',
            'Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier'
        );
    }
}
