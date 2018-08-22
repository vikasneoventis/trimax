<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model;

use Aitoc\MultiLocationInventory\Api\Data\SupplierProductInterface;

class SupplierProduct extends AbstractModel implements SupplierProductInterface
{
    /**
     * Initialize model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\MultiLocationInventory\Model\ResourceModel\SupplierProduct');
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
    public function getSupplierId()
    {
        return $this->getData(self::SUPPLIER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSupplierId($id)
    {
        return $this->setData(self::SUPPLIER_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($id)
    {
        return $this->setData(self::PRODUCT_ID, $id);
    }
}
