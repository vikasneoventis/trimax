<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model;

use Aitoc\MultiLocationInventory\Api\Data\WarehouseStockItemInterface;

class WarehouseStockItem extends AbstractModel implements WarehouseStockItemInterface
{
    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem');
    }

    /**
     * Return entity id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getEntityId();
    }

    /**
     * Set id of entity
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setEntityId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStockItemId()
    {
        return $this->getData(self::STOCK_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStockItemId($itemId)
    {
        return $this->setData(self::STOCK_ITEM_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     */
    public function getWarehouseId()
    {
        return $this->getData(self::WAREHOUSE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWarehouseId($warehouseId)
    {
        return $this->setData(self::WAREHOUSE_ID, $warehouseId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getParLevel()
    {
        return $this->getData(self::PAR_LEVEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setParLevel($qty)
    {
        return $this->setData(self::PAR_LEVEL, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function getSafetyStock()
    {
        return $this->getData(self::SAFETY_STOCK);
    }

    /**
     * {@inheritdoc}
     */
    public function setSafetyStock($qty)
    {
        return $this->setData(self::SAFETY_STOCK, $qty);
    }
}
