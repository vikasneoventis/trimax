<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel;

class WarehouseOrderItem extends \Aitoc\MultiLocationInventory\Model\ResourceModel\AbstractResource
{

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('aitoc_mli_order_item_warehouse', 'entity_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /*TODO: replace existing data */
//        $orderItemId = $object->getOrderItemId();
//        $select = $this->getConnection()->select()->from($this->getMainTable());
//        $select->where('order_item_id = ?', $orderItemId);
//
//        $test = $this->getConnection()->fetchRow($select);
//        if ($test) {
//            
//        }
        return $this;
    }
}
