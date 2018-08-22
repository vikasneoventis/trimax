<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * InstallSchema constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $orderTable = $installer->getTable('sales_order');
        $installer->run("ALTER TABLE `{$orderTable}` ADD `status_preorder` VARCHAR (50) NOT NULL AFTER `status`;");
        $installer->run("UPDATE `{$orderTable}` SET `status_preorder`=`status`;");
        $installer->run("UPDATE `{$orderTable}` SET `status`='pending' WHERE `status`='pendingpreorder';");
        $installer->run("UPDATE `{$orderTable}` SET `status`='processing' WHERE `status`='processingpreorder';");

        $orderGridTable = $installer->getTable('sales_order_grid');
        $installer->run("ALTER TABLE `{$orderGridTable}` ADD `status_preorder` VARCHAR (50) NOT NULL AFTER `status`;");
        $installer->run("UPDATE `{$orderGridTable}` SET `status_preorder`=`status`;");
        $installer->run("UPDATE `{$orderGridTable}` SET `status`='pending' WHERE `status`='pendingpreorder';");
        $installer->run("UPDATE `{$orderGridTable}` SET `status`='processing' WHERE `status`='processingpreorder';");

        $statusTable = $installer->getTable('sales_order_status');
        $data = [
            ['status' => 'pendingpreorder', 'label' => 'Pending Pre-Order'],
            ['status' => 'processingpreorder', 'label' => 'Processing Pre-Order']
        ];
        $installer->getConnection()->insertArray($statusTable, ['status', 'label'], $data);
    }
}
