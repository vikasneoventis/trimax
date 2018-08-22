<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Api;

/**
 * Warehouse CRUD interface.
 * @api
 */
interface WarehouseRepositoryInterface
{
    /**
     * Save warehouse.
     *
     * @param \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface $warehouse
     * @return \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\WarehouseInterface $warehouse);

    /**
     * Retrieve warehouse.
     *
     * @param int $warehouseId
     * @return \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($warehouseId);

    /**
     * Retrieve warehouses matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Cms\Api\Data\BlockSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete warehouse.
     *
     * @param \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface $warehouse
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\WarehouseInterface $warehouse);

    /**
     * Delete warehouse by ID.
     *
     * @param int $warehouseId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($warehouseId);
}
