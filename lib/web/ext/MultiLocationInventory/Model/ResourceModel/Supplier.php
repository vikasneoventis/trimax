<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel;

class Supplier extends \Aitoc\MultiLocationInventory\Model\ResourceModel\AbstractResource
{

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('aitoc_mli_supplier', 'entity_id');
    }

//    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
//    {
//        $orderItemId = $object->getOrderItemId();
//        $select = $this->getConnection()->select()->from($this->getMainTable());
//        $select->where('order_item_id = ?', $orderItemId);
//
//        $test = $this->getConnection()->fetchRow($select);
//        if ($test) {
//            /*TODO: replace existing data */
//        }
//        return $this;
//    }
}
