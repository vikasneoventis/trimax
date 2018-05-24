<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Subcategories\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $catalogSetupFactory;

    public function __construct(CategorySetupFactory $categorySetupFactory)
    {
        $this->catalogSetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $catalogSetup = $this->catalogSetupFactory->create(['setup' => $setup]);

        $catalogSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'thumbnail',
            [
                'group'         => 'General Information',
                'input'         => 'image',
                'type'          => 'varchar',
                'label'         => 'Thumbnail',
                'backend'       => \Magento\Catalog\Model\Category\Attribute\Backend\Image::class,
                'visible'       => true,
                'sort_order'    => 6,
                'required'      => false,
                'visible_on_front' => true,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL

            ]
        );
    }
}