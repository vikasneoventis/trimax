<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Model;

use Magento\Framework\Model\AbstractModel;
use Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface;

class General extends AbstractModel implements GeneralInterface
{
    /**
     * Prefix for events
     * @var string
     */
    protected $_eventPrefix = 'aitoc_productunitsandquantities_general';

    protected function _construct()
    {
        $this->_init('Aitoc\ProductUnitsAndQuantities\Model\ResourceModel\General');
    }

    /**
     * Return entity id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getItemId();
    }

    /**
     * Set id of entity
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setItemId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemId()
    {
        return $this->getData(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemId($id)
    {
        return $this->setData(self::ITEM_ID, $id);
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
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * {@inheritdoc}
     */
    public function getReplaceQty()
    {
        return $this->getData(self::REPLACE_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setReplaceQty($inputTypeId)
    {
        return $this->setData(self::REPLACE_QTY, $inputTypeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyType()
    {
        return $this->getData(self::QTY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyType($qtyTypeId)
    {
        return $this->setData(self::QTY_TYPE, $qtyTypeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getUseQuantities()
    {
        return $this->getData(self::USE_QUANTITIES);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseQuantities($value)
    {
        return $this->setData(self::USE_QUANTITIES, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStartQty()
    {
        return $this->getData(self::START_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartQty($startQty)
    {
        return $this->setData(self::START_QTY, $startQty);
    }

    /**
     * {@inheritdoc}
     */
    public function getQtyIncrement()
    {
        return $this->getData(self::QTY_INCREMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setQtyIncrement($qtyIncrement)
    {
        return $this->setData(self::QTY_INCREMENT, $qtyIncrement);
    }

    /**
     * {@inheritdoc}
     */
    public function getEndQty()
    {
        return $this->getData(self::END_QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setEndQty($endQty)
    {
        return $this->setData(self::END_QTY, $endQty);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowUnits()
    {
        return $this->getData(self::ALLOW_UNITS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowUnits($value)
    {
        return $this->setData(self::ALLOW_UNITS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPricePer()
    {
        return $this->getData(self::PRICE_PER);
    }

    /**
     * {@inheritdoc}
     */
    public function setPricePer($unit)
    {
        return $this->setData(self::PRICE_PER, $unit);
    }

    /**
     * {@inheritdoc}
     */
    public function getDivider()
    {
        return $this->getData(self::DIVIDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setDivider($divider)
    {
        return $this->setData(self::DIVIDER, $divider);
    }

    /**
     * {@inheritdoc}
     */
    public function getUseConfigParams()
    {
        return $this->getData(self::USE_CONFIG_PARAMS);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseConfigParams($value)
    {
        return $this->setData(self::USE_CONFIG_PARAMS, $value);
    }
}
