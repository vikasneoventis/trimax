<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model;

use Aitoc\MultiLocationInventory\Api\WarehouseRepositoryInterface;
use Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface;
use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse as ResourceWarehouse;
use Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\CollectionFactory as WarehouseCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class WarehouseRepository implements WarehouseRepositoryInterface
{
    /**
     * @var ResourceWarehouse
     */
    protected $warehouseResource;

    /**
     * @var WarehouseFactory
     */
    protected $warehouseFactory;

    /**
     * @var array
     */
    private $warehouses = [];

    /**
     * @var WarehouseCollectionFactory
     */
    protected $warehouseCollectionFactory;

    public function __construct(
        ResourceWarehouse $warehouseResource,
        WarehouseFactory $warehouseFactory,
        WarehouseCollectionFactory $collectionFactory
    ) {
        $this->warehouseResource = $warehouseResource;
        $this->warehouseFactory = $warehouseFactory;
        $this->warehouseCollectionFactory = $collectionFactory;
    }

    /**
     * Save warehouse.
     *
     * @param \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface $warehouse
     *
     * @return \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(WarehouseInterface $warehouse)
    {
        if ($warehouse->getRuleId()) {
            $warehouse = $this->get($warehouse->getWarehouseId())->addData($warehouse->getData());
        }

        try {
            $this->warehouseResource->save($warehouse);
            unset($this->warehouses[$warehouse->getId()]);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save warehouse %1', $warehouse->getWarehouseId()));
        }
        return $warehouse;
    }

    /**
     * {@inheritdoc}
     */
    public function get($warehouseId)
    {
        if (!isset($this->warehouses[$warehouseId])) {
            /** @var \Magento\CatalogRule\Model\Rule $rule */
            $warehouse = $this->warehouseFactory->create();

            /* TODO: change to resource model after entity manager will be fixed */
            $warehouse->load($warehouseId);
            if (!$warehouse->getWarehouseId()) {
                throw new NoSuchEntityException(__('Warehouse with specified ID "%1" not found.', $warehouseId));
            }
            $this->warehouses[$warehouseId] = $warehouse;
        }
        return $this->warehouses[$warehouseId];
    }

    /**
     * Retrieve warehouse.
     *
     * @param int $warehouseId
     *
     * @return \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($warehouseId)
    {
        $warehouse = $this->warehouseFactory->create();
        $this->resource->load($warehouse, $warehouseId);
        if (!$warehouse->getId()) {
            throw new NoSuchEntityException(__('Warehouse with id "%1" does not exist.', $warehouseId));
        }
        return $warehouse;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->warehouseCollectionFactory->create();

        /** @var SortOrder $sortOrder */
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field = $sortOrder->getField();
            $collection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->load();

        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * Delete warehouse.
     *
     * @param \Aitoc\MultiLocationInventory\Api\Data\WarehouseInterface $warehouse
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(WarehouseInterface $warehouse)
    {
        try {
            $this->resource->delete($warehouse);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete warehouse by ID.
     *
     * @param int $warehouseId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($warehouseId)
    {
        return $this->delete($this->getById($warehouseId));
    }
}
