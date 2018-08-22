<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\ProductUnitsAndQuantities\Api;

/**
 * Warehouse CRUD interface.
 * @api
 */
interface GeneralRepositoryInterface
{
    /**
     * Save model.
     *
     * @param \Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface $generalModel
     * @return \Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\GeneralInterface $generalModel);

    /**
     * Retrieve model.
     *
     * @param int $entityId
     * @return \Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($entityId);

    /**
     * Delete model.
     *
     * @param \Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface $generalModel
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\GeneralInterface $generalModel);

    /**
     * Delete entity by ID.
     *
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
