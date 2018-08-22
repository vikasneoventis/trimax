<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Api\Data;

interface WarehouseOrderItemInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID         = 'entity_id';
    const ORDER_ITEM_ID     = 'order_item_id';
    const WAREHOUSE_ID      = 'warehouse_id';
    const QTY               = 'qty';
    /**#@-*/

    /**
     * @return int|null
     */
    public function getEntityId();

    /**
     * @param int $id
     *
     * @return bool
     */
    public function setEntityId($id);

    /**
     * @return int
     */
    public function getOrderItemId();

    /**
     * @param int $itemId
     *
     * @return $this
     */
    public function setOrderItemId($itemId);

    /**
     * @return int
     */
    public function getWarehouseId();

    /**
     * @param int $warehouseId
     *
     * @return $this
     */
    public function setWarehouseId($warehouseId);

    /**
     * @return int
     */
    public function getQty();

    /**
     * @param int $qty
     *
     * @return $this
     */
    public function setQty($qty);
}
