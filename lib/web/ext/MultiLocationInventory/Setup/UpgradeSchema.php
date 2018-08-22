<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade the MultiLocationInventory DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.1.1', '<')) {
            $this->createOrderItemWarehouseTable($setup);
        }

        if (version_compare($context->getVersion(), '0.1.2', '<')) {
            $this->createStockItemWarehouseTable($setup);
        }

        if (version_compare($context->getVersion(), '0.1.3', '<')) {
            $this->addParLevelFields($setup);
        }

        if (version_compare($context->getVersion(), '0.1.4', '<')) {
            $this->createSupplierTable($setup);
        }

        if (version_compare($context->getVersion(), '0.1.5', '<')) {
            $this->createSupplierProductTable($setup);
        }

        if (version_compare($context->getVersion(), '0.1.7', '<')) {
            $this->upgradeWarehouseStockQtyField($setup);
        }

        if (version_compare($context->getVersion(), '0.1.8', '<')) {
            $this->addCanReceiveEmailFieldToSupplier($setup);
        }

        $setup->endSetup();
    }

    /**
     * Create table 'order_item_warehouse'
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createOrderItemWarehouseTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_order_item_warehouse')
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'order_item_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Item ID'
        )->addColumn(
            'warehouse_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Warehouse ID'
        )->addColumn(
            'qty',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Qty'
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_order_item_warehouse',
                'warehouse_id',
                'aitoc_mli_warehouse',
                'warehouse_id'
            ),
            'warehouse_id',
            $installer->getTable('aitoc_mli_warehouse'),
            'warehouse_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_order_item_warehouse',
                'order_item_id',
                'sales_order_item',
                'item_id'
            ),
            'order_item_id',
            $installer->getTable('sales_order_item'),
            'item_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Relation table for warehouse and order item'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'stock_item_warehouse'
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createStockItemWarehouseTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_stock_item_warehouse')
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'stock_item_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Stock Item ID'
        )->addColumn(
            'warehouse_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Warehouse ID'
        )->addColumn(
            'qty',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Qty'
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_stock_item_warehouse',
                'warehouse_id',
                'aitoc_mli_warehouse',
                'warehouse_id'
            ),
            'warehouse_id',
            $installer->getTable('aitoc_mli_warehouse'),
            'warehouse_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_stock_item_warehouse',
                'stock_item_id',
                'cataloginventory_stock_item',
                'item_id'
            ),
            'stock_item_id',
            $installer->getTable('cataloginventory_stock_item'),
            'item_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Warehouse stock table'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Add par level fields to 'stock_item_warehouse' table
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function addParLevelFields($installer)
    {
        $warehouseStockItemTable = $installer->getTable('aitoc_mli_stock_item_warehouse');
        $connection = $installer->getConnection();
        $connection->addColumn($warehouseStockItemTable, 'safety_stock', Table::TYPE_INTEGER);
        $connection->addColumn($warehouseStockItemTable, 'par_level', Table::TYPE_INTEGER);
    }

    /**
     * Create table 'supplier'
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createSupplierTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_supplier')
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Title'
        )->addColumn(
            'contact_name',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Contact Name'
        )->addColumn(
            'phone',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Phone'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Email'
        )->addColumn(
            'address',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false],
            'Address'
        )->setComment(
            'Supplier table'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create relation table for products and suppliers
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createSupplierProductTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_supplier_product')
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'supplier_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Supplier ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product ID'
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_supplier_product',
                'supplier_id',
                'aitoc_mli_supplier',
                'entity_id'
            ),
            'supplier_id',
            $installer->getTable('aitoc_mli_supplier'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_supplier_product',
                'product_id',
                'catalog_product_entity',
                'entity_id'
            ),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Supplier/Product relation table'
        );

        $installer->getConnection()->createTable($table);
    }

    public function upgradeWarehouseStockQtyField($setup)
    {
        $setup->getConnection()->changeColumn(
            $setup->getTable('aitoc_mli_stock_item_warehouse'),
            'qty',
            'qty',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => false
            ]
        );
    }

    public function addCanReceiveEmailFieldToSupplier($setup)
    {
        $supplierTable = $setup->getTable('aitoc_mli_supplier');
        $connection = $setup->getConnection();
        $connection->addColumn($supplierTable, 'can_receive_email', Table::TYPE_SMALLINT);
    }
}
