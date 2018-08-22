<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model;

use Aitoc\MultiLocationInventory\Api\Data\WarehouseOrderItemInterface;

class WarehouseOrderItem extends AbstractModel implements WarehouseOrderItemInterface
{
    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseOrderItem');
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
    public function getOrderItemId()
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItemId($itemId)
    {
        return $this->setData(self::ORDER_ITEM_ID, $itemId);
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
}
