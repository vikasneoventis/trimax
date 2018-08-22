<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseOrderItem;

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
            'Aitoc\MultiLocationInventory\Model\WarehouseOrderItem',
            'Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseOrderItem'
        );
    }

    /**
     * Add tax class
     *
     * @param $fieldsToSelect
     *
     * @return $this
     */
    public function addWarehouseDataToSelect($fieldsToSelect = [])
    {
        $this->getSelect()->joinLeft(
            ['warehouse' => $this->getTable('aitoc_mli_warehouse')],
            "main_table.warehouse_id = warehouse.warehouse_id",
            $fieldsToSelect
        );
        return $this;
    }
}
