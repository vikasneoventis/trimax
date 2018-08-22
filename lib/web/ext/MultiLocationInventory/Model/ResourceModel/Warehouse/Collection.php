<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse\Collection\AbstractCollection
{
    /**
     * @var string
     * @SuppressWarnings(PHPMD.CamelCasePropertyName)
     */
    protected $_idFieldName = 'warehouse_id';

    /**
     * Warehouse associated with warehouse entities information map
     *
     * @var array
     */
    protected $_associatedEntitiesMap;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_associatedEntitiesMap = $this->getAssociatedEntitiesMap();
    }

    /**
     * Define resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\MultiLocationInventory\Model\Warehouse',
            'Aitoc\MultiLocationInventory\Model\ResourceModel\Warehouse'
        );
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->mapAssociatedEntities('store', 'store_ids');
        $this->mapAssociatedEntities('customer_group', 'customer_group_ids');

        $this->setFlag('add_stores_to_result', false);
        return parent::_afterLoad();
    }

    /**
     * @param string $entityType
     * @param string $objectField
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function mapAssociatedEntities($entityType, $objectField)
    {
        if (!$this->_items) {
            return;
        }

        $entityInfo = $this->_getAssociatedEntityInfo($entityType);
        $warehouseIdField = $entityInfo['warehouse_id_field'];
        $entityIds = $this->getColumnValues($warehouseIdField);

        $select = $this->getConnection()->select()->from(
            $this->getTable($entityInfo['associations_table'])
        )->where(
            $warehouseIdField . ' IN (?)',
            $entityIds
        );

        $associatedEntities = $this->getConnection()->fetchAll($select);

        array_map(function ($associatedEntity) use ($entityInfo, $warehouseIdField, $objectField) {
            $item = $this->getItemByColumnValue($warehouseIdField, $associatedEntity[$warehouseIdField]);
            $itemAssociatedValue = $item->getData($objectField) === null ? [] : $item->getData($objectField);
            $itemAssociatedValue[] = $associatedEntity[$entityInfo['entity_id_field']];
            $item->setData($objectField, $itemAssociatedValue);
        }, $associatedEntities);
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

    public function prepareCollectionForOrderItems()
    {
        $this->getSelect()->joinLeft(
            ['wh_groups' => $this->getTable('aitoc_mli_warehouse_group')],
            "main_table.warehouse_id = wh_groups.warehouse_id",
            ['customer_group_id']
        );
        $this->getSelect()->joinLeft(
            ['wh_stores' => $this->getTable('aitoc_mli_warehouse_store')],
            "main_table.warehouse_id = wh_stores.warehouse_id",
            ['store_id']
        );

        return $this;
    }
}
