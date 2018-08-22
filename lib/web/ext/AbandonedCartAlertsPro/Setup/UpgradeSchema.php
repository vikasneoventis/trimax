<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\AbandonedCartAlertsPro\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade the CatalogRule module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->addTemplateIdField($setup);
            $this->addSendIntervalField($setup);
        }

        $setup->endSetup();
    }

    /**
     * Add template_id field to Capmaign table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    private function addTemplateIdField(SchemaSetupInterface $setup)
    {
        $campaignTable = $setup->getTable('aitoc_abandoned_cart_alerts_pro_campaign');
        $templateField = 'template_id';
        $connection = $setup->getConnection();
        $connection->addColumn($campaignTable, 'template_id', 'int');
        $connection->addForeignKey(
            $setup->getFkName(
                $campaignTable,
                $templateField,
                'email_template',
                $templateField
            ),
            $campaignTable,
            'campaign_id',
            $setup->getTable('aitoc_abandoned_cart_alerts_pro_campaign'),
            'campaign_id'
        );
    }

    /**
     * Add send_interval field to Capmaign table
     *
     * @param SchemaSetupInterface $setup
     *
     * @return void
     */
    private function addSendIntervalField(SchemaSetupInterface $setup)
    {
        $campaignTable = $setup->getTable('aitoc_abandoned_cart_alerts_pro_campaign');
        $connection = $setup->getConnection();
        $connection->addColumn($campaignTable, 'send_interval', 'int');
    }
}
