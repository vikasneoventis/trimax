<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aitoc\MultiLocationInventory\Helper\Warehouse as WarehouseHelper;

class Warehouse extends \Aitoc\MultiLocationInventory\Model\ResourceModel\AbstractResource
{
    /**
     * @var StockFactory
     */
    protected $stockFactory;

    /**
     * Warehouse to customer groups linkage table
     *
     * @var string
     */
    protected $warehouseCustomerGroupTable;

    /**
     * Warehouse to store linkage table
     *
     * @var string
     */
    protected $warehouseStoreTable;

    /**
     * Warehouse emails table
     *
     * @var string
     */
    protected $warehouseEmailTable;

    /**
     * @var \Magento\Framework\EntityManager\EntityManager
     */
    protected $entityManager;

    /**
     * @var WarehouseHelper
     */
    protected $warehouseHelper;
    
    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        WarehouseHelper $warehouseHelper,
        $connectionName = null
    ) {
        $this->warehouseHelper = $warehouseHelper;
        $this->_associatedEntitiesMap = $this->getAssociatedEntitiesMap();
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('aitoc_mli_warehouse', 'warehouse_id');
    }

    /**
     * Warehouse Customer Group table name getter
     *
     * @return string
     */
    public function getWarehouseCustomerGroupTable()
    {
        if (!$this->warehouseCustomerGroupTable) {
            $this->warehouseCustomerGroupTable = $this->getTable('mli_warehouse_group');
        }
        return $this->warehouseCustomerGroupTable;
    }

    /**
     * Warehouse Customer Group table name getter
     *
     * @return string
     */
    public function getWarehouseStoreTable()
    {
        if (!$this->warehouseStoreTable) {
            $this->warehouseStoreTable = $this->getTable('mli_warehouse_store');
        }
        return $this->warehouseStoreTable;
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->getEntityManager()->save($object);
        if ($object->getIsDefault()) {
            $this->warehouseHelper->disableDefaultWarehouse([$object->getWarehouseId()]);
        }
        return $this;
    }

    /**
     * Warehouse Customer Group table name getter
     *
     * @return string
     */
    public function getWarehouseEmailsTable()
    {
        if (!$this->warehouseEmailTable) {
            $this->warehouseEmailTable = $this->getTable('mli_warehouse_email');
        }
        return $this->warehouseEmailTable;
    }

    /**
     * Retrieve warehouse customer groups identifiers
     *
     * @param \Aitoc\MultiLocationInventory\Model\Warehouse|int $warehouse
     * @return array
     */
    public function getCustomerGroupsIds($warehouse)
    {
        $connection = $this->getConnection();

        if ($warehouse instanceof \Aitoc\MultiLocationInventory\Model\Warehouse) {
            $warehouseId = $warehouse->getWarehouseId();
        } else {
            $warehouseId = $warehouse;
        }

        $select = $connection->select()->from(
            $this->getWarehouseCustomerGroupTable(),
            'customer_group_id'
        )->where(
            'warehouse_id = ?',
            (int)$warehouseId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Retrieve warehouse store identifiers
     *
     * @param \Aitoc\MultiLocationInventory\Model\Warehouse|int $warehouse
     * @return array
     */
    public function getStoreIds($warehouse)
    {
        $connection = $this->getConnection();

        if ($warehouse instanceof \Aitoc\MultiLocationInventory\Model\Warehouse) {
            $warehouseId = $warehouse->getWarehouseId();
        } else {
            $warehouseId = $warehouse;
        }

        $select = $connection->select()->from(
            $this->getWarehouseStoreTable(),
            'store_id'
        )->where(
            'warehouse_id = ?',
            (int)$warehouseId
        );

        return $connection->fetchCol($select);
    }

    /**
     * @return array
     * @deprecated
     */
    private function getAssociatedEntitiesMap()
    {
        if (!$this->_associatedEntitiesMap) {
            $this->_associatedEntitiesMap = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\AssociatedEntityMap')
                ->getData();
        }
        return $this->_associatedEntitiesMap;
    }

    /**
     * @return \Magento\Framework\EntityManager\EntityManager
     * @deprecated
     */
    private function getEntityManager()
    {
        if (null === $this->entityManager) {
            $this->entityManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\EntityManager::class);
        }
        return $this->entityManager;
    }
}
