<?php

namespace Pektsekye\ProductExploded\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        
        $installer->getConnection()->dropTable($installer->getTable('productexploded_link'));
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('productexploded_link')
            )
            ->addColumn(
                'link_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Link ID'
            )
            ->addColumn(
                'number_on_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => true, 'default' => null],
                'Number on image'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'productexploded_link',
                    'link_id',
                    'catalog_product_link',
                    'link_id'
                ),
                'link_id',
                $installer->getTable('catalog_product_link'),
                'link_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )            
            ->setComment(
                'ProductExploded Link Table'
            );
        $installer->getConnection()
            ->createTable($table);


        $installer->getConnection()->dropTable($installer->getTable('productexploded_label'));
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('productexploded_label')
            )
            ->addColumn(
                'label_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Label ID'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Product ID'
            ) 
            ->addColumn(
                'width',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Width'
            )                    
            ->addColumn(
                'height',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Height'
            )                
            ->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Title'
            )            
            ->addColumn(
                'link_to_number',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => true, 'default' => null],
                'Link to Number'
            )                  
            ->addColumn(
                'x',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Position Left'
            )                    
            ->addColumn(
                'y',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Position Top'
            )
            ->addForeignKey(
                $installer->getFkName('productexploded_label', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )         
            ->setComment(
                'ProductExploded Label Table'
            );
        $installer->getConnection()
            ->createTable($table);

   
        $setup->endSetup();

    }
}
