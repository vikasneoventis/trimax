<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\MultiLocationInventory\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createMainTable($installer);
        $this->createWarehouseStoreRelationTable($installer);
        $this->createWarehouseEmailRelationTable($installer);
        $this->createWarehouseGroupRelationTable($installer);

        $installer->endSetup();
    }

    /**
     * Create table 'aitoc_mli_warehouse'
     * remind: Default values is also in warehouse edit Block
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createMainTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_warehouse')
        )->addColumn(
            'warehouse_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Warehouse ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Name'
        )->addColumn(
            'is_default',
            Table::TYPE_BOOLEAN,
            1,
            ['default' => 0, 'nullable' => false],
            'Is default'
        )->addColumn(
            'priority',
            Table::TYPE_SMALLINT,
            null,
            ['default' => 0, 'unsigned' => false, 'nullable' => false],
            'Priority for rules'
        )->addColumn(
            'is_visible_in_checkout',
            Table::TYPE_BOOLEAN,
            1,
            ['default' => 0, 'nullable' => false],
            'Visible on checkout'
        )->addColumn(
            'is_visible_in_product',
            Table::TYPE_BOOLEAN,
            1,
            ['default' => 0, 'nullable' => false],
            'Visible on product page'
        )->addColumn(
            'is_visible_in_order',
            Table::TYPE_BOOLEAN,
            1,
            /** remind: Default values is also in warehouse edit Block */
            ['default' => 1, 'nullable' => false],
            'Visible in order, invoice'
        )->addColumn(
            'is_visible_in_shipment',
            Table::TYPE_BOOLEAN,
            1,
            ['default' => 1, 'nullable' => false],
            'Visible in shipment, refund'
        )->addColumn(
            'order_notification_status',
            Table::TYPE_BOOLEAN,
            1,
            ['default' => 0, 'nullable' => false],
            'Is Order notifications enabled'
        )->addColumn(
            'low_stock_notification_status',
            Table::TYPE_BOOLEAN,
            1,
            ['default' => 0, 'nullable' => false],
            'Is Low stock notification enabled'
        )->addColumn(
            'country_id',
            Table::TYPE_TEXT,
            50,
            ['nullable' => false],
            'Country'
        )->addColumn(
            'region_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => true, 'default' => null],
            'State/Province'
        )->addColumn(
            'city',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'City'
        )->addColumn(
            'street',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Street Address'
        )->addColumn(
            'postcode',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Zip/Postal Code'
        )->addColumn(
            'telephone',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Phone Number'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Email'
        )->addColumn(
            'latitude',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Latitude'
        )->addColumn(
            'longitude',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Longitude'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            null,
            ['nullable' => true, 'default' => null],
            'Description'
        )->setComment(
            'Warehouse entity'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'aitoc_mli_warehouse_store'
     */
    private function createWarehouseStoreRelationTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_warehouse_store')
        )->addColumn(
            'warehouse_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Warehouse ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store ID'
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_warehouse_store',
                'warehouse_id',
                'aitoc_mli_warehouse',
                'warehouse_id'
            ),
            'warehouse_id',
            $installer->getTable('aitoc_mli_warehouse'),
            'warehouse_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('aitoc_mli_warehouse_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Warehouse store'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'aitoc_mli_warehouse_email'
     */
    private function createWarehouseEmailRelationTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_warehouse_email')
        )->addColumn(
            'warehouse_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Warehouse ID'
        )->addColumn(
            'email',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email for notices'
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_warehouse_email',
                'warehouse_id',
                'aitoc_mli_warehouse',
                'warehouse_id'
            ),
            'warehouse_id',
            $installer->getTable('aitoc_mli_warehouse'),
            'warehouse_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Warehouse email list for notices'
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table 'aitoc_mli_warehouse_group'
     */
    private function createWarehouseGroupRelationTable($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('aitoc_mli_warehouse_group')
        )->addColumn(
            'warehouse_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Warehouse ID'
        )->addColumn(
            'customer_group_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store View ID'
        )->addForeignKey(
            $installer->getFkName(
                'aitoc_mli_warehouse_group',
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
                'aitoc_mli_warehouse_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Warehouse customer group'
        );

        $installer->getConnection()->createTable($table);
    }
}
