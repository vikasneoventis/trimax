<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Setup;

use Magento\Eav\Setup\EavSetup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'preorder',
            [
                'label'        => 'Pre-Order',
                'visible'      => 0,
                'required'     => 0,
                'position'     => 1,
                'type'         => 'int',
                'input'        => '0',
                'default'      => '0',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'preorderdescript',
            [
                'label'        => 'Pre-Order Note',
                //'group'        => 'General',
                'visible'      => 0,
                'required'     => 0,
                'position'     => 1,
                'type'         => 'varchar',
                'input'        => '0',
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Sales\Model\Order::ENTITY,
            'status_preorder',
            [
                'label'        => 'Pre-Order Status',
                'required'     => 0,
                'type'         => 'static',
                'input'        => 'text',
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'backstock_preorders',
            [
            'label'                 => 'Backstock Pre-Orders',
            'type'                  => 'int',
            //'group'                 => 'General',
            'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'               => false,
            'required'              => false,
            'user_defined'          => false,
            'default'               => '0',
            'used_in_product_listing' => true,
            'is_configurable'       => false
            ]
        );
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'backorders_change',
            [
                'label'                 => 'Set product as Pre-Order when product gets Out-of-Stock',
                'type'                  => 'int',
                //'group'                 => 'General',
                'global'                => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible'               => false,
                'required'              => false,
                'user_defined'          => false,
                'default'               => '0',
                'used_in_product_listing' => true,
                'is_configurable'       => false,
            ]
        );


        $preorderId = $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'preorder', 'attribute_id');
        $preorderscriptId = $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'preorderdescript', 'attribute_id');

        $aitquantitymanagerTable = null;
        $updatePreorder = false;

        $inventoryTable = null;
        $updateInventory = false;

        $aitquantitymanagerTable = 'aitoc_cataloginventory_stock_item';

        if ($aitquantitymanagerTable && $setup->tableExists($aitquantitymanagerTable)) {
            $updatePreorder = true;
        }

        try {
            $inventoryTable = $setup->getTable('cataloginventory_stock_item');
            $updateInventory = $setup->tableExists($inventoryTable);
        } catch (\Exception $e) {
            $inventoryTable = null;
            $updateInventory = false;
        }

        $attributeIds = [];

        if ($preorderId) {
            $attributeIds[] = $preorderId;
        }

        if ($preorderscriptId) {
            $attributeIds[] = $preorderscriptId;
        }

        if ($updatePreorder && count($attributeIds)) {
            $setup->run('UPDATE' . $setup->getTable('catalog_eav_attribute') . 'SET is_global = ' . \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE . ' WHERE attribute_id IN (' . implode(',', $attributeIds) . ')');
        }
        $setup->startSetup();

        if ($updatePreorder && $preorderId) {
            $setup->run('UPDATE ' . $aitquantitymanagerTable . 'SET backorders = ' . \Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS . ' WHERE product_id IN (SELECT DISTINCT CPEI.entity_id FROM ' . $setup->getTable('catalog_product_entity_int') . ' AS CPEI WHERE CPEI.attribute_id = ' . $preorderId . ' AND CPEI.value = 1);');
        }

        if ($updateInventory && $preorderId) {
            $setup->run('UPDATE ' . $inventoryTable . ' SET backorders = ' . \Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS . ' WHERE product_id IN (SELECT DISTINCT CPEI.entity_id FROM ' . $setup->getTable('catalog_product_entity_int') . ' AS CPEI WHERE CPEI.attribute_id = ' . $preorderId . ' AND CPEI.value = 1);');
        }

        $preorderscriptId = $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'preorderdescript', 'attribute_id');

        if ($preorderscriptId) {
            $setup->run(' UPDATE ' . $setup->getTable('catalog_eav_attribute') . ' SET is_global = ' . \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE . ' WHERE	attribute_id = ' . $preorderscriptId);
        }
        $setup->endSetup();

    }
}
