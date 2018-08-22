<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Api\Data;

interface GeneralInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ITEM_ID = 'item_id';
    const PRODUCT_ID = 'product_id';
    const REPLACE_QTY = 'replace_qty';
    const QTY_TYPE = 'qty_type';
    const USE_QUANTITIES = 'use_quantities';
    const START_QTY = 'start_qty';
    const QTY_INCREMENT = 'qty_increment';
    const END_QTY = 'end_qty';
    const ALLOW_UNITS = 'allow_units';
    const PRICE_PER = 'price_per';
    const DIVIDER = 'price_per_divider';
    const USE_CONFIG_PARAMS = 'use_config_params';

    /**
     * @return int|null
     */
    public function getItemId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setItemId($id);

    /**
     * @return int|null
     */
    public function getProductId();

    /**
     * @param int $productId
     *
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @return int
     */
    public function getReplaceQty();

    /**
     * @param int $inputTypeId
     *
     * @return $this
     */
    public function setReplaceQty($inputTypeId);

    /**
     * @return int
     */
    public function getQtyType();

    /**
     * @param int $qtyTypeId
     *
     * @return $this
     */
    public function setQtyType($qtyTypeId);

    /**
     * @return string
     */
    public function getUseQuantities();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUseQuantities($value);

    /**
     * @return float
     */
    public function getStartQty();

    /**
     * @param float $startQty
     *
     * @return $this
     */
    public function setStartQty($startQty);

    /**
     * @return float
     */
    public function getQtyIncrement();

    /**
     * @param float $qtyIncrement
     *
     * @return $this
     */
    public function setQtyIncrement($qtyIncrement);

    /**
     * @return float
     */
    public function getEndQty();

    /**
     * @param float $endQty
     *
     * @return $this
     */
    public function setEndQty($endQty);

    /**
     * @return int
     */
    public function getAllowUnits();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setAllowUnits($value);

    /**
     * @return string
     */
    public function getPricePer();

    /**
     * @param string $unit
     *
     * @return $this
     */
    public function setPricePer($unit);

    /**
     * @return string
     */
    public function getDivider();

    /**
     * @param string $divider
     *
     * @return $this
     */
    public function setDivider($divider);

    /**
     * @return string
     */
    public function getUseConfigParams();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUseConfigParams($value);
}
