<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    const NAME_ATTRIBUTE_ID = 73;

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    protected $coreHelper;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Aitoc\MultiLocationInventory\Helper\Core $coreHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->coreHelper = $coreHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
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
            'Aitoc\MultiLocationInventory\Model\WarehouseStockItem',
            'Aitoc\MultiLocationInventory\Model\ResourceModel\WarehouseStockItem'
        );
        $this->addFilterToMap('warehouse_id', 'main_table.warehouse_id');
        $this->addFilterToMap('entity_id', 'main_table.entity_id');
    }

    /**
     * Add warehouse data to select
     *
     * @param $fieldsToSelect
     *
     * @return $this
     */
    public function addWarehouseDataToSelect($fieldsToSelect = [])
    {
        $this->getSelect()->joinLeft(
            ['warehouse' => $this->getTable('aitoc_mli_warehouse')],
            "main_table.warehouse_id = warehouse.warehouse_id",
            $fieldsToSelect
        );
        return $this;
    }

    /**
     * Add product data to select
     *
     * @param $fieldsToSelect
     *
     * @return $this
     */
    public function addProductDataToSelect($fieldsToSelect = [])
    {
        $this->getSelect()->joinLeft(
            ['cataloginventory_stock_item' => $this->getTable('cataloginventory_stock_item')],
            'main_table.stock_item_id = cataloginventory_stock_item.item_id',
            ['product_id']
        );
        $this->getSelect()->joinLeft(
            ['catalog_product' => $this->getTable('catalog_product_entity')],
            "cataloginventory_stock_item.product_id = catalog_product.entity_id",
            $fieldsToSelect
        );
        if ($this->coreHelper->isEnterpriseEdition()) {
            $this->getSelect()->joinLeft(
                ['attribute_varchar' => $this->getTable('catalog_product_entity_varchar')],
                "attribute_varchar.row_id = catalog_product.entity_id AND attribute_varchar.attribute_id = " . $this::NAME_ATTRIBUTE_ID,
                ['product_name' => 'value']
            );
        } else {
            $this->getSelect()->joinLeft(
                ['attribute_varchar' => $this->getTable('catalog_product_entity_varchar')],
                "attribute_varchar.entity_id = catalog_product.entity_id AND attribute_varchar.attribute_id = " . $this::NAME_ATTRIBUTE_ID,
                ['product_name' => 'value']
            );
        }
        return $this;
    }

    /**
     * Add supplier data to select
     *
     * @param $fieldsToSelect
     *
     * @return $this
     */
    public function addSupplierDataToSelect($fieldsToSelect = [])
    {
        $this->addProductDataToSelect(['product_sku' => 'sku']);
        $this->getSelect()->joinLeft(
            ['supplier_product' => $this->getTable('aitoc_mli_supplier_product')],
            'cataloginventory_stock_item.product_id = supplier_product.product_id',
            ['supplier_id']
        );
        $this->getSelect()->joinLeft(
            ['supplier' => $this->getTable('aitoc_mli_supplier')],
            "supplier_product.supplier_id = supplier.entity_id",
            $fieldsToSelect
        );
        return $this;
    }

    public function prepareCollectionForParLevel()
    {
        $this->getSelect()->where('main_table.qty <= main_table.par_level and (main_table.par_level > 0 or main_table.safety_stock > 0) and main_table.safety_stock > main_table.par_level');
        $this->addExpressionFieldToSelect(
            'qty_to_order',
            '({{safety_stock}} - {{qty}})',
            [
                'safety_stock'=>'main_table.safety_stock',
                'qty'=>'main_table.qty'
            ]
        );
        $this->addWarehouseDataToSelect(['warehouse_name' => 'name'])
            ->addSupplierDataToSelect(['supplier_title' => 'title']);

        return $this;
    }
}
