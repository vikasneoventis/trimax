<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_CMS_DISPLAY_RULES
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
namespace Itoris\CmsDisplayRules\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public $magentoConfigTable = 'core_config_data';

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Itoris\EmailTemplates\Helper\Data $helper */
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->create('Itoris\CmsDisplayRules\Helper\Data');
        $setup->startSetup();
        
        $con = $setup->getConnection();
        $tmp = $con->fetchRow("SHOW COLUMNS FROM `{$setup->getTable('customer_group')}` where Field = 'customer_group_id'");
        $groupFkType = $tmp['Type']; //compatibility with M2.2
        
        if(!$setup->tableExists($setup->getTable('itoris_cms_display_rules_page'))) {
            $setup->run(" create table {$setup->getTable('itoris_cms_display_rules_page')} (
                            `page_id` smallint(6) not null primary key,
                            `start_date` date null,
                            `finish_date` date null,
                            `another_cms` int not null,
                            foreign key (`page_id`) references {$setup->getTable('cms_page')} (`page_id`) on delete cascade on update cascade
                        ) engine = InnoDB default charset = utf8;");
        }
        if(!$setup->tableExists($setup->getTable('itoris_cms_display_rules_page_group'))) {
            $setup->run("create table {$setup->getTable('itoris_cms_display_rules_page_group')} (
                            `page_id` smallint(6) not null,
                            `group_id` {$groupFkType} not null,
                            foreign key (`page_id`) references {$setup->getTable('itoris_cms_display_rules_page')} (`page_id`) on delete cascade on update cascade,
                            foreign key (`group_id`) references {$setup->getTable('customer_group')} (`customer_group_id`) on delete cascade on update cascade
                        ) engine = InnoDB default charset = utf8;");
        }
        if(!$setup->tableExists($setup->getTable('itoris_cms_display_rules_block'))) {
            $setup->run("  create table {$setup->getTable('itoris_cms_display_rules_block')} (
                            `block_id` smallint(6) not null primary key,
                            `start_date` date null,
                            `finish_date` date null,
                            `another_cms` int not null,
                            foreign key (`block_id`) references {$setup->getTable('cms_block')} (`block_id`) on delete cascade on update cascade
                        ) engine = InnoDB default charset = utf8;");
        }
        if(!$setup->tableExists($setup->getTable('itoris_cms_display_rules_block_group'))) {
            $setup->run(" create table {$setup->getTable('itoris_cms_display_rules_block_group')} (
                            `block_id` smallint(6) not null,
                            `group_id` {$groupFkType} not null,
                            foreign key (`block_id`) references {$setup->getTable('itoris_cms_display_rules_block')} (`block_id`) on delete cascade on update cascade,
                            foreign key (`group_id`) references {$setup->getTable('customer_group')} (`customer_group_id`) on delete cascade on update cascade
                        ) engine = InnoDB default charset = utf8;");
        }
        $setup->endSetup();
        $configNote = $helper->getBackendConfig()->getValue($helper::XML_PATH_MODULE_ENABLED);
        if(!isset($configNote)){
            $setup->run("
            INSERT INTO {$setup->getTable($this->magentoConfigTable)}
            (path, value)
            VALUES('".$helper::XML_PATH_MODULE_ENABLED."', '1')
            ");
        }
    }
}
