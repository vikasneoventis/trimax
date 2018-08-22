<?php
namespace Aitoc\ProductUnitsAndQuantities\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.1.1', '<')) {
            $this->createOrderItemUnitsTable($setup);
        }
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addQtyTypeField($setup);
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            $this->addFieldsForDynamicQtyRange($setup);
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->addUseConfigParamsField($setup);
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->changeColumnTypesInBaseTable($setup);
        }
        $setup->endSetup();
    }

    /**
     * Create 'aitoc_product_units_and_quantities_orders' table
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function createOrderItemUnitsTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('aitoc_product_units_and_quantities_orders')
        )->addColumn(
            'item_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Item Id'
        )->addColumn(
            'order_item_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Product Id'
        )->addColumn(
            'price_per',
            Table::TYPE_TEXT,
            255,
            [],
            'Price Per'
        )->addColumn(
            'price_per_divider',
            Table::TYPE_TEXT,
            255,
            [],
            'Price Per Divider'
        )->setComment(
            'Aitoc ProductUnitsAndQuantities orders table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Add 'qty_type' field to the 'aitoc_product_units_and_quantities' table
     *
     * @param SchemaSetupInterface $setup
     */
    public function addQtyTypeField(SchemaSetupInterface $setup)
    {
        $baseTable = $setup->getTable('aitoc_product_units_and_quantities');
        $connection = $setup->getConnection();
        $connection->addColumn($baseTable, 'qty_type', Table::TYPE_SMALLINT);
    }


    /**
     * Add additional fields to the 'aitoc_product_units_and_quantities' table
     * Fields: start_qty, qty_increment, end_qty
     *
     * @param SchemaSetupInterface $setup
     */
    public function addFieldsForDynamicQtyRange(SchemaSetupInterface $setup)
    {
        $baseTable = $setup->getTable('aitoc_product_units_and_quantities');
        $connection = $setup->getConnection();
        $connection->addColumn($baseTable, 'start_qty', Table::TYPE_INTEGER);
        $connection->addColumn($baseTable, 'qty_increment', Table::TYPE_INTEGER);
        $connection->addColumn($baseTable, 'end_qty', Table::TYPE_INTEGER);
    }

    /**
     * Add 'use_config_params' field to the 'aitoc_product_units_and_quantities' table
     *
     * @param SchemaSetupInterface $setup
     */
    public function addUseConfigParamsField(SchemaSetupInterface $setup)
    {
        $baseTable = $setup->getTable('aitoc_product_units_and_quantities');
        $connection = $setup->getConnection();
        $connection->addColumn($baseTable, 'use_config_params', Table::TYPE_TEXT);
    }

    /**
     * Change fields types to valid types on the 'aitoc_product_units_and_quantities' table
     * Fields: replace_qty, allow_units, start_qty, qty_increment, end_qty
     *
     * @param SchemaSetupInterface $setup
     */
    public function changeColumnTypesInBaseTable(SchemaSetupInterface $setup)
    {
        $baseTable = $setup->getTable('aitoc_product_units_and_quantities');
        $connection = $setup->getConnection();
        $this->changeColumnType($connection, $baseTable, 'replace_qty', Table::TYPE_INTEGER);
        $this->changeColumnType($connection, $baseTable, 'allow_units', Table::TYPE_SMALLINT);
        $this->changeColumnType($connection, $baseTable, 'start_qty', Table::TYPE_FLOAT);
        $this->changeColumnType($connection, $baseTable, 'qty_increment', Table::TYPE_FLOAT);
        $this->changeColumnType($connection, $baseTable, 'end_qty', Table::TYPE_FLOAT);

    }

    /**
     * @param AdapterInterface $connection
     * @param $table
     * @param $column
     * @param $type
     */
    private function changeColumnType(AdapterInterface $connection, $table, $column, $type)
    {
        $connection->changeColumn(
            $table,
            $column,
            $column,
            [
                'type' => $type,
            ]
        );
    }
}
