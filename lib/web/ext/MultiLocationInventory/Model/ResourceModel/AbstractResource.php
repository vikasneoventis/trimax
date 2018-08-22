<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

/**
 * Abstract Warehouse entity resource model
 *
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel;

abstract class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store associated with warehouse entities information map
     * @var array
     */
    protected $_associatedEntitiesMap = [];

    /**
     * Bind specified warehouses to entities
     *
     * @param int[]|int|string $warehouseIds
     * @param int[]|int|string $entityIds
     * @param string $entityType
     * @return $this
     * @throws \Exception
     */
    public function bindWarehouseToEntity($warehouseIds, $entityIds, $entityType)
    {
        $this->getConnection()->beginTransaction();

        try {
            $this->_multiplyBunchInsert($warehouseIds, $entityIds, $entityType);
        } catch (\Exception $e) {
            $this->getConnection()->rollback();
            throw $e;
        }

        $this->getConnection()->commit();

        return $this;
    }

    /**
     * Multiply warehouse ids by entity ids and insert
     *
     * @param int|[] $warehouseIds
     * @param int|[] $entityIds
     * @param string $entityType
     * @return $this
     */
    protected function _multiplyBunchInsert($warehouseIds, $entityIds, $entityType)
    {
        if (empty($warehouseIds) || empty($entityIds)) {
            return $this;
        }
        if (!is_array($warehouseIds)) {
            $warehouseIds = [(int)$warehouseIds];
        }
        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        $data = [];
        $count = 0;
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        $this->getConnection()->delete(
            $this->getTable($entityInfo['associations_table']),
            $this->getConnection()->quoteInto(
                $entityInfo['warehouse_id_field'] . ' IN (?)',
                $warehouseIds
            )
        );
        foreach ($warehouseIds as $warehouseId) {
            foreach ($entityIds as $entityId) {
                $data[] = [
                    $entityInfo['entity_id_field'] => $entityId,
                    $entityInfo['warehouse_id_field'] => $warehouseId,
                ];
                $count++;
                if ($count % 1000 == 0) {
                    $this->getConnection()->insertMultiple(
                        $this->getTable($entityInfo['associations_table']),
                        $data
                    );
                    $data = [];
                }
            }
        }
        if (!empty($data)) {
            $this->getConnection()->insertMultiple(
                $this->getTable($entityInfo['associations_table']),
                $data
            );
        }
        return $this;
    }

    /**
     * Unbind specified warehouses from entities
     *
     * @param int[]|int|string $warehouseIds
     * @param int[]|int|string $entityIds
     * @param string $entityType
     * @return $this
     */
    public function unbindWarehouseFromEntity($warehouseIds, $entityIds, $entityType)
    {
        $connection = $this->getConnection();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($entityIds)) {
            $entityIds = [(int)$entityIds];
        }
        if (!is_array($warehouseIds)) {
            $warehouseIds = [(int)$warehouseIds];
        }

        $where = [];
        if (!empty($warehouseIds)) {
            $where[] = $connection->quoteInto($entityInfo['warehouse_id_field'] . ' IN (?)', $warehouseIds);
        }
        if (!empty($entityIds)) {
            $where[] = $connection->quoteInto($entityInfo['entity_id_field'] . ' IN (?)', $entityIds);
        }

        $connection->delete($this->getTable($entityInfo['associations_table']), implode(' AND ', $where));

        return $this;
    }

    /**
     * Retrieve warehouse's associated entity Ids by entity type
     *
     * @param int $warehouseId
     * @param string $entityType
     * @return array
     */
    public function getAssociatedEntityIds($warehouseId, $entityType)
    {
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table']),
            [$entityInfo['entity_id_field']]
        )->where(
            $entityInfo['warehouse_id_field'] . ' = ?',
            $warehouseId
        );

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Retrieve store ids of specified warehouse
     *
     * @param int $warehouseId
     * @return array
     */
    public function getStoreIds($warehouseId)
    {
        return $this->getAssociatedEntityIds($warehouseId, 'store');
    }

    /**
     * Retrieve customer group ids of specified warehouse
     *
     * @param int $warehouseId
     * @return array
     */
    public function getCustomerGroupIds($warehouseId)
    {
        return $this->getAssociatedEntityIds($warehouseId, 'customer_group');
    }

    /**
     * Retrieve correspondent entity information (associations table name, columns names)
     * of warehouse's associated entity by specified entity type
     *
     * @param string $entityType
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getAssociatedEntityInfo($entityType)
    {
        if (isset($this->_associatedEntitiesMap[$entityType])) {
            return $this->_associatedEntitiesMap[$entityType];
        }

        throw new \Magento\Framework\Exception\LocalizedException(
            __('There is no information about associated entity type "%1".', $entityType)
        );
    }
}
