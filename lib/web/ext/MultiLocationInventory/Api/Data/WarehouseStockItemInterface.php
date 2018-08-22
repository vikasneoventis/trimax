<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Api\Data;

interface WarehouseStockItemInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID         = 'entity_id';
    const STOCK_ITEM_ID     = 'stock_item_id';
    const WAREHOUSE_ID      = 'warehouse_id';
    const QTY               = 'qty';
    const PAR_LEVEL         = 'par_level';
    const SAFETY_STOCK      = 'safety_stock';
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
    public function getStockItemId();

    /**
     * @param int $itemId
     *
     * @return $this
     */
    public function setStockItemId($itemId);

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

    /**
     * @return int
     */
    public function getParLevel();

    /**
     * @param int $qty
     *
     * @return $this
     */
    public function setParLevel($qty);

    /**
     * @return int
     */
    public function getSafetyStock();

    /**
     * @param int $qty
     *
     * @return $this
     */
    public function setSafetyStock($qty);
}
